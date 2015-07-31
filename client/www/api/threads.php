<?php
/**
 * /api/apikey.php?api_key=<api_key>
 */

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

if(!($select_statement = $db->prepare("SELECT namespace_id FROM email_namespace_mapping WHERE mailx_account_id = ?"))) {
    show_db_error();
}

if(!$select_statement->bind_param("s", $account_id)) {
    show_db_error();
}

if(!$select_statement->execute()) {
    show_db_error();
}

$namespace_ids = array();
$out_namespace_id = NULL;

if(!$select_statement->bind_result($out_namespace_id)) {
    show_db_error();
}

while($select_statement->fetch()) {
    array_push($namespace_ids, $out_namespace_id);
}

echo json_encode(array(
    "success" => true,
    "body" => array(
        "namespace_ids" => $namespace_ids,
        "threads" => array()
    )
));

$db->close();
