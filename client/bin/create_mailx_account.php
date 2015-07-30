#!/usr/bin/env php
<?php
include "../etc/config.php";
error_reporting(E_ERROR | E_PARSE);

if(sizeof($argv) < 3) {
	die("Usage: " . $argv[0] . " <account_name> <password>\n");
}

echo "Adding new MailX account\n";

$account_name = $argv[1];
$accont_password = $argv[2];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
	die("Unable to connect to MailX database: " . $db->connect_error);
}

if(!($count_statement = $db->prepare("SELECT COUNT(*) FROM mailx_account WHERE name = ?"))) {
	die("Count statement preparation failed: " . $count_statement->error . "\n");
}

if(!$count_statement->bind_param("s", $account_name)) {
	die("Parameters binding on count statement failed: " . $count_statement->error . "\n");
}

if(!$count_statement->execute()) {
	die("Count statement execution failed: " . $count_statement->error . "\n");	
}

$account_count = 0;

$out_account_count = NULL;

if(!$count_statement->bind_result($out_account_count)) {
	die("Output binding on count statement failed: " . $count_statement->error . "\n");
}

while($count_statement->fetch()) {
	$account_count = $out_account_count;
}

if($account_count != 0) {
	die("Account name already exists\n");
}

if(!($insert_statement = $db->prepare("INSERT INTO mailx_account (name, password) VALUES (?, ?)"))) {
	die("Insert statement preparation failed: " . $insert_statement->error . "\n");
}

if(!$insert_statement->bind_param("ss", $account_name, hash('sha512', $account_password))) {
	die("Parameters binding on insert statement failed: " . $insert_statement->error . "\n");
}

if(!$insert_statement->execute()) {
	die("Insert statement execution failed: " . $insert_statement->error . "\n");
}

echo "Added " . $account_name . "\n";

$insert_statement->close();
$db->close();

echo "Exiting\n";