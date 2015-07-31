<?php
header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/auth.php";
include "libs/error.php";

if(!isset($_POST["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_POST["api_key"];

if(!isset($_POST["message"])) {
    show_invalid_params_error();
}

$message_json = $_POST["message"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

echo json_encode(array(
    "success" => true,
    "body" => array(
        "message" => "Message sent successfully"
    )
));

$db->close();