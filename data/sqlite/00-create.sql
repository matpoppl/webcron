
DROP TABLE IF EXISTS `{DBPREFIX}cron_tasks`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}cron_tasks` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`type` VARCHAR(200) NOT NULL,
	`name` VARCHAR(200) NOT NULL,
	`params` TEXT
);

DROP TABLE IF EXISTS `{DBPREFIX}cron_task_triggers`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}cron_task_triggers` (
	`id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`id_task` INTEGER NOT NULL,
	`active` BOOLEAN NOT NULL DEFAULT '1',
	
	`repeat_type` INTEGER NOT NULL,
	`repeat_every` INTEGER NOT NULL,
	
	-- ISO 8601 numeric representation of the day of the week 1-monday
	`weekdays` VARCHAR(7) NULL DEFAULT NULL,
	
	-- DATE_W3C/DATE_ATOM 2005-08-15T15:52:01+00:00
	`from` DATETIME NOT NULL,
	`to` VARCHAR(25) NULL,
	`next` VARCHAR(25) NULL
);

DROP TABLE IF EXISTS `{DBPREFIX}cron_running`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}cron_running` (
	`id_task` INTEGER NOT NULL PRIMARY KEY,
	`created` VARCHAR(25) NOT NULL,
	`iteration` INTEGER NOT NULL,
	`params` TEXT NULL
);

DROP TABLE IF EXISTS `{DBPREFIX}translations`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}translations` (
	`locale` VARCHAR(5) NOT NULL,
	`domain` TEXT NOT NULL,
	`msgid` TEXT NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY(`locale`, `domain`, `msgid`)
);

DROP TABLE IF EXISTS `{DBPREFIX}users`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}users` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`username` VARCHAR(256) NOT NULL,
	`roles` TEXT NOT NULL,
	UNIQUE(`username`)
);

DROP TABLE IF EXISTS `{DBPREFIX}user_tokens`;

CREATE TABLE IF NOT EXISTS `{DBPREFIX}user_tokens` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT,
	`id_user` VARCHAR(256) NOT NULL,
	-- DATE_W3C/DATE_ATOM 2005-08-15T15:52:01+00:00
	`modified` VARCHAR(25) NOT NULL,
	`type` VARCHAR(14) CHECK( `type` IN ('password', 'password_reset') ) NOT NULL,
	`token` VARCHAR(256) NOT NULL,
	FOREIGN KEY(`id_user`) REFERENCES `{DBPREFIX}users`(`id`)
);

CREATE UNIQUE INDEX `idx_user_tokens_1` ON `{DBPREFIX}user_tokens` (`id_user`, `type`); 

INSERT INTO `{DBPREFIX}users` (`id`, `username`, `roles`) VALUES (1, 'johndoe', ',admin,');

-- PASSWORD=hello-world
INSERT INTO `{DBPREFIX}user_tokens` (`id`, `id_user`, `modified`, `type`, `token`) VALUES (1, 1, '1970-01-01T00:00:00+00:00', 'password', '$1$$BWemhGJwUO5iVc9hT.LD31');
