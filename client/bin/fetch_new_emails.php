#!/usr/bin/env php
<?php
include "../etc/config.php";
error_reporting(E_ERROR | E_PARSE);

echo "Pulling in new email accounts\n";

$json_file_content = file_get_contents(API_ROOT . "n");
$json_result = json_decode($json_file_content);

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno > 0) {
	die("Unable to connect to MailX database: " . $db->connect_error);
}

if(!($count_statement = $db->prepare("SELECT COUNT(*) FROM account WHERE email_address = ? AND namespace_id = ?"))) {
	die("Count statement preparation failed: " . $count_statement->error . "\n");
}

$new_email_namespace_mappings = array();

foreach($json_result as $json_record) {

	$email_address = $json_record->email_address;
	$namespace_id = $json_record->namespace_id;

	if(!$count_statement->bind_param("ss", $email_address, $namespace_id)) {
		die("Parameters binding on count statement failed: " . $count_statement->error . "\n");
	}

	if(!$count_statement->execute()) {
		die("Count statement execution failed: " . $count_statement->error . "\n");	
	}

	$mapping_count = 0;

	$out_mapping_count = NULL;

	if(!$count_statement->bind_result($out_mapping_count)) {
		die("Output binding on count statement failed: " . $count_statement->error . "\n");
	}

	while($count_statement->fetch()) {
		$mapping_count = $out_mapping_count;
	}

	if($mapping_count == 0) {
		$new_email_namespace_mappings[$email_address] = $namespace_id;
	}
}

$count_statement->close();

if(!($insert_statement = $db->prepare("INSERT INTO account (email_address, namespace_id, password, api_key) VALUES (?, ?, ?, ?)"))) {
	die("Insert statement preparation failed: " . $insert_statement->error . "\n");
}

// TODO: Generate default password and send it to the email address
function generate_default_password() {
	return "123";
}

foreach ($new_email_namespace_mappings as $email_address => $namespace_id) {
	if(!$insert_statement->bind_param("ssss", $email_address, $namespace_id, hash("sha512", generate_default_password()), md5(uniqid($email_address, true)))) {
		die("Parameters binding on insert statement failed: " . $insert_statement->error . "\n");
	}

	if(!$insert_statement->execute()) {
		die("Insert statement execution failed: " . $insert_statement->error . "\n");
	}

	echo "Added " . $email_address . "\n";
}

$insert_statement->close();
$db->close();

echo "Exiting\n";