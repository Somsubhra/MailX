<?php
header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/auth.php";
include "libs/error.php";

if(!isset($_GET["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_GET["api_key"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account_id = get_account_id_from_api_key($api_key, $db);

if(!($select_statement = $db->prepare("SELECT email_address FROM email_namespace_mapping WHERE mailx_account_id = ?"))) {
    show_db_error();
}

if(!$select_statement->bind_param("s", $account_id)) {
    show_db_error();
}

if(!$select_statement->execute()) {
    show_db_error();
}

$email_addresses = array();
$out_email_address = NULL;

if(!$select_statement->bind_result($out_email_address)) {
    show_db_error();
}

while($select_statement->fetch()) {
    array_push($email_addresses, $out_email_address);
}

echo json_encode(array(
    "success" => true,
    "body" => array(
        "email_ids" => $email_addresses
    )
));

$select_statement->close();
$db->close();