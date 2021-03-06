<?php
/**
 * /api/apikey.php?email_address=<email_address>&password=<password>
 */

header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/error.php";

error_reporting(E_ERROR | E_PARSE);

if(!isset($_GET["email_address"]) || !isset($_GET["password"])) {
    show_invalid_params_error();
}

$account_email_address = $_GET["email_address"];
$account_password = $_GET["password"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

if(!($select_statement = $db->prepare("SELECT api_key FROM account WHERE email_address = ? AND password = ?"))) {
    show_db_error();
}

if(!$select_statement->bind_param("ss", $account_email_address, hash("sha512", $account_password))) {
    show_db_error();
}

if(!$select_statement->execute()) {
    show_db_error();
}

$api_key = "";
$out_api_key = NULL;

if(!$select_statement->bind_result($out_api_key)) {
    show_db_error();
}

while($select_statement->fetch()) {
    $api_key = $out_api_key;
}

if($api_key == "") {
    show_error("Invalid credentials");
}

echo json_encode(array(
    "success" => true,
    "body" => array(
        "api_key" => $api_key
    )
));

$select_statement->close();
$db->close();