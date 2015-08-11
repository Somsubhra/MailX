<?php
/**
 * POST /api/send.php?api_key=<api_key>&name=<name>&email=<email>&message=<message>&subject=<subject>
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

if(!isset($_POST["subject"])) {
    show_invalid_params_error();
}

$subject = $_POST["subject"];

if(!isset($_POST["name"])) {
    show_invalid_params_error();
}

$name = $_POST["name"];

if(!isset($_POST["email"])) {
    show_invalid_params_error();
}

$email = $_POST["email"];

$to_list = array();

array_push($to_list, array(
    "name" => $name,
    "email" => $email
));

$participants = "";

if(isset($_POST["participants"])) {
    $participants = $_POST["participants"];
}

$participants_list = explode(",", $participants);

foreach($participants_list as $participant) {
    if(!filter_var($participant, FILTER_VALIDATE_EMAIL) === false) {
        array_push($to_list, array(
            "name" =>  "",
            "email" => $participant
        ));
    }
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

$account = get_account_from_api_key($api_key, $db);
$namespace_id = $account["namespace_id"];

$urlToPost = API_ROOT . "n/$namespace_id/send";

$ch = curl_init($urlToPost);

curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json"
    ),
    CURLOPT_POSTFIELDS => json_encode(array(
        "body" => "<div class='mailx-sent-message'>" . $message . "</div>",
        "subject" => $subject,
        "to" => $to_list
    ))
));

$response = curl_exec($ch);

if($response == FALSE) {
    show_error("cURL error");
}

curl_close($ch);

echo json_encode(array(
    "success" => true,
    "body" => array(
        "message" => "Message sent successfully"
    )
));

$db->close();