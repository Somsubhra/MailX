<?php
/**
 * /api/deltacursor.php?api_key=<api_key>
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

$ch = curl_init(API_ROOT . "n/$namespace_id/delta/generate_cursor");
curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
    ),
    CURLOPT_POSTFIELDS => json_encode(array(
        "start" => time()
    ))
));

$response = curl_exec($ch);

if($response == FALSE) {
    show_error("cURL error");
}

$response = json_decode($response);

echo json_encode(array(
    "success" => true,
    "body" => $response
));

$db->close();