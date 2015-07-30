<?php
include "../../etc/config.php";
error_reporting(E_ERROR | E_PARSE);

if(!isset($_POST["name"]) && !isset($_POST["password"])) {
	header("location: ../index.php");
}

$name = $_POST["name"];
$password = $_POST["password"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!($auth_statement = $db->prepare("SELECT id FROM mailx_account WHERE name = ? AND password = ?"))) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!$auth_statement->bind_param("ss", $name, hash('sha512', $password))) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

if(!$auth_statement->execute()) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

$account_id = -1;
$out_account_id = NULL;

if(!$auth_statement->bind_result($out_account_id)) {
    header("location: ../error.php?code=DB_ERR");
    exit();
}

while($auth_statement->fetch()) {
    $account_id = $out_account_id;
}

if($account_id == -1) {
    session_start();
    $_SESSION["LOGIN_ERROR"] = "Please enter correct username and password.";
    session_write_close();
    header("location: ../index.php");
    exit();
}

session_start();
$_SESSION["MAILX_ID"] = $account_id;
$_SESSION["LOGGED_IN"] = "true";
session_write_close();

header("location: ../home.php");

$auth_statement->close();
$db->close();
