<?php
/**
 * POST /api/reply.php?api_key=<api_key>&message=<message>&thread_id=<thread_id>
 */

header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/auth.php";
include "libs/error.php";

error_reporting(E_ERROR | E_PARSE);

if(!isset($_POST["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_POST["api_key"];

if(!isset($_POST["message"])) {
    show_invalid_params_error();
}

$message = $_POST["message"];

if(!isset($_POST["thread_id"])) {
    show_invalid_params_error();
}

$thread_id = $_POST["thread_id"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

// Create the recipients list
$thread_json_content = file_get_contents(API_ROOT . "n/$namespace_id/threads/$thread_id");
$thread_json = json_decode($thread_json_content);

$recipients = array();

$thread_participants = $thread_json->participants;

foreach($thread_participants as $thread_participant) {
    if($thread_participant->email == $account["email_address"]) {
        continue;
    }

    array_push($recipients, $thread_participant);
}

// Create the draft
$urlToPost = API_ROOT . "n/$namespace_id/drafts";

$ch = curl_init($urlToPost);

curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode(array(
        "thread_id" => $thread_id,
        "to" => $recipients,
        "body" => $message,
        "version" => 0
    ))
));

$response = curl_exec($ch);

if($response == FALSE) {
    show_error("cURL error");
}

$response_json = json_decode($response, true);
$draft_id = $response_json["id"];

curl_close($ch);

// Send the draft
$urlToPost = API_ROOT . "n/$namespace_id/send";

$ch = curl_init($urlToPost);

curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode(array(
        "draft_id" => $draft_id,
        "version" => 0
    ))
));

$response = curl_exec($ch);

if($response == FALSE) {
    show_error("cURL error");
}

die($response);

curl_close($ch);

echo json_encode(array(
    "success" => true,
    "body" => array(
        "message" => "Message sent successfully"
    )
));

$db->close();