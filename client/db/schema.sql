DROP DATABASE IF EXISTS mailx;
CREATE DATABASE IF NOT EXISTS mailx;
USE mailx;

CREATE TABLE IF NOT EXISTS `email_namespace_mapping` (
	`email_address` VARCHAR(255) NOT NULL,
	`namespace_id` VARCHAR(30) NOT NULL,
	PRIMARY KEY (`email_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;