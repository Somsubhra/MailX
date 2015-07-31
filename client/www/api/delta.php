<?php
/**
 * /api/delta.php?api_key=<api_key>&cursor=<cursor>
 */

header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/auth.php";
include "libs/error.php";

if(!isset($_GET["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_GET["api_key"];

if(!isset($_GET["cursor"])) {
    show_invalid_params_error();
}

$cursor = $_GET["cursor"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

$deltas_json_content = file_get_contents(API_ROOT . "n/$namespace_id/delta?cursor=$cursor");
$deltas_json = json_decode($deltas_json_content);

echo json_encode(array(
    "success" => true,
    "body" => array(
        "deltas" => $deltas_json
    )
));

$db->close();