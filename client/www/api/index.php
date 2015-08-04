<?php
/**
 * /api/index.php?api_key=<api_key>
 */

header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/auth.php";
include "libs/error.php";

error_reporting(E_ERROR | E_PARSE);

if(!isset($_GET["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_GET["api_key"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

$account_json_content = file_get_contents(API_ROOT . "n/$namespace_id");
$account_json = json_decode($account_json_content);

echo json_encode(array(
    "success" => true,
    "body" => array(
        "account" => $account_json
    )
));

$db->close();
