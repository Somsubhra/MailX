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

if(!($select_statement = $db->prepare("SELECT name FROM mailx_account WHERE id = ?"))) {
    show_db_error();
}

if(!$select_statement->bind_param("s", $account_id)) {
    show_db_error();
}

if(!$select_statement->execute()) {
    show_db_error();
}

$account_name = "";
$out_account_name = NULL;

if(!$select_statement->bind_result($out_account_name)) {
    show_db_error();
}

while($select_statement->fetch()) {
    $account_name = $out_account_name;
}

echo json_encode(array(
    "success" => true,
    "body" => array(
        "message" => "Ground zero!",
        "requester" => $account_name
    )
));

$select_statement->close();
$db->close();