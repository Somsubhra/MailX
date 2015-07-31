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

$api_key = $_GET["api_key"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

$threads_json_content = file_get_contents(API_ROOT . "n/" . $namespace_id .  "/threads");
$threads_json = json_decode($threads_json_content);

usort($threads, function($thread1, $thread2) {
    $ts1 = $thread1->last_message_timestamp;
    $ts2 = $thread2->last_message_timestamp;
    return $ts2 - $ts1;
});

echo json_encode(array(
    "success" => true,
    "body" => array(
        "threads" => $threads_json
    )
));

$db->close();
