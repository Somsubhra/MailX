DROP DATABASE IF EXISTS mailx;
CREATE DATABASE IF NOT EXISTS mailx;
USE mailx;

CREATE TABLE IF NOT EXISTS `account` (
	`id` BIGINT(10) NOT NULL AUTO_INCREMENT,
	`email_address` CHAR(255) NOT NULL UNIQUE,
	`password` CHAR(128) NOT NULL,
	`api_key` CHAR(32) NOT NULL,
	`namespace_id` CHAR(30) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
