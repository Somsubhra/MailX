<?php
/**
 * /api/messages.php?api_key=<api_key>&thread_id=<thread_id>
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

if(!isset($_GET["thread_id"])) {
    show_invalid_params_error();
}

$thread_id = $_GET["thread_id"];

$limit = 20;

if(isset($_GET["limit"])) {
    if(is_numeric($_GET["limit"])) {
        $limit = $_GET["limit"];
    }
}

$offset = 0;

if(isset($_GET["offset"])) {
    if(is_numeric($_GET["offset"])) {
        $offset = $_GET["offset"];
    }
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

$messages_json_content = file_get_contents(API_ROOT . "n/$namespace_id/messages?thread_id=$thread_id&limit=$limit&offset=$offset");
$messages_json = json_decode($messages_json_content);

echo json_encode(array(
    "success" =>  true,
    "body" => array(
        "messages" => $messages_json
    )
));

$db->close();