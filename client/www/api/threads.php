<?php
header("Content-Type: application/json");

include "../../etc/config.php";
include "libs/error.php";

if(!isset($_GET["api_key"])) {
    show_invalid_key_error();
}

$api_key = $_GET["api_key"];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
    show_db_error();
}

echo json_encode(array(
    "success" => true,
    "body" => array(
        "threads" => array()
    )
));

$db->close();
