#!/usr/bin/env php
<?php
include "../etc/config.php";
error_reporting(E_ERROR | E_PARSE);

if(sizeof($argv) < 3) {
	die("Usage: " . $argv[0] . " <email_address> <account_name>\n");
}

echo "Associating email address with MailX account\n";

$email_address = $argv[1];
$account_name = $argv[2];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
	die("Unable to connect to MailX database: " . $db->connect_error);
}

if(!($count_statement = $db->prepare("SELECT COUNT(*) FROM email_namespace_mapping WHERE email_address = ?"))) {
	die("Count statement preparation failed: " . $count_statement->error . "\n");
}

if(!$count_statement->bind_param("s", $email_address)) {
	die("Parameters binding on coint statement failed: " . $count_statement->error . "\n");
}

if(!$count_statement->execute()) {
	die("Count statement execution failed: " . $count_statement->error . "\n");
}

$email_address_count = 0;

$out_email_address_count = NULL;

if(!$count_statement->bind_result($out_email_address_count)) {
	die("Output binding on count statement failed: " . $count_statement->error . "\n");
}

while($count_statement->fetch()) {
	$email_address_count = $out_email_address_count;
}

if($email_address_count == 0) {
	die("Email address " . $email_address . " does not exist\n");
}

$count_statement->close();

if(!($select_statement = $db->prepare("SELECT id FROM mailx_account WHERE name = ?"))) {
	die("Select statement preparation failed: " . $select_statement->error . "\n");
}

if(!$select_statement->bind_param("s", $account_name)) {
	die("Parameters binding on select statement failed: " . $select_statement->error . "\n");
}

if(!$select_statement->execute()) {
	die("Select statement execution failed: " . $select_statement->error . "\n");
}

$account_id = -1;

$out_account_id = NULL;

if(!$select_statement->bind_result($out_account_id)) {
	die("Output binding on select statement failed: " . $select_statement->error . "\n");
}

while($select_statement->fetch()) {
	$account_id = $out_account_id;
}

if($account_id == -1) {
	die("Account with given name does not exist\n");
}

$select_statement->close();

if(!$update_statement = $db->prepare("UPDATE email_namespace_mapping SET mailx_account_id = ? WHERE email_address = ?")) {
	die("Update statement preparation failed: " . $update_statement->error . "\n");
}

if(!$update_statement->bind_param("ss", $account_id, $email_address)) {
	die("Parameters binding on update statement failed: " . $update_statement->error . "\n");
}

if(!$update_statement->execute()) {
	die("Update statement execution failed: " . $update_statement->error . "\n");
}

echo "Associated " . $email_address . " with MailX account " . $account_name . "\n";

$update_statement->close();
$db->close();

echo  "Exiting\n";