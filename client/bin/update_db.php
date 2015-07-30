#!/usr/bin/env php
<?php
include "../etc/config.php";
error_reporting(E_ERROR | E_PARSE);

echo "Pulling in new email accounts...\n";

$json_file_content = file_get_contents(API_ROOT . "n");
$json_result = json_decode($json_file_content);

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
	die("Unable to connect to MailX database: " . $db->connect_error);
}

foreach($json_result as $json_record) {
	var_dump($json_record);
}

$db->close();

echo "Exiting...\n";