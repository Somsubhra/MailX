<?php
include "../../etc/config.php";

error_reporting(E_ERROR | E_PARSE);

if(!isset($_POST["emailaddress"]) && !isset($_POST["password"])) {
	header("location: ../index.php");
}

$email_address = $_POST["emailaddress"];
$password = $_POST["password"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!($auth_statement = $db->prepare("SELECT id, api_key FROM account WHERE email_address = ? AND password = ?"))) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!$auth_statement->bind_param("ss", $email_address, hash('sha512', $password))) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!$auth_statement->execute()) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

$account_id = -1;
$out_account_id = NULL;

$api_key = -1;
$out_api_key = NULL;

if(!$auth_statement->bind_result($out_account_id, $out_api_key)) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

while($auth_statement->fetch()) {
    $account_id = $out_account_id;
    $api_key = $out_api_key;
}

if($account_id == -1) {
    session_start();
    $_SESSION["LOGIN_ERROR"] = "Please enter correct email and password.";
    session_write_close();
    header("location: ../index.php");
    exit();
}

session_start();
$_SESSION["MAILX_ID"] = $account_id;
$_SESSION["MAILX_API_KEY"] = $api_key;
$_SESSION["MAILX_LOGGED_IN"] = "true";

session_write_close();

header("location: ../home.php");

$auth_statement->close();
$db->close();
