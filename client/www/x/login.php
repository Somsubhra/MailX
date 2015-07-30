<?php
include "../../etc/config.php";

if(!isset($_POST["email"]) && !isset($_POST["password"])) {
	header("location: ../index.php");
}

$email_address = $_POST["email"];
$password = $_POST["password"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    header("../error.php?code=DB_ERR");
}

