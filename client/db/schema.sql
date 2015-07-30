DROP DATABASE IF EXISTS mailx;
CREATE DATABASE IF NOT EXISTS mailx;
USE mailx;

CREATE TABLE IF NOT EXISTS `mailx_account` (
	`id` BIGINT(10) NOT NULL,
	`name` CHAR(255) NOT NULL UNIQUE,
	`password` CHAR(128) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `email_namespace_mapping` (
	`email_address` CHAR(255) NOT NULL,
	`namespace_id` CHAR(30) NOT NULL,
	`mailx_account_id` BIGINT(10),
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified` TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`email_address`),
	FOREIGN KEY (`mailx_account_id`) REFERENCES mailx_account(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;