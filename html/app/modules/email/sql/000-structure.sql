
DROP TABLE IF EXISTS `{DBPREFIX}email_transports`;

CREATE TABLE `{DBPREFIX}email_transports` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT(200) NOT NULL,
  `email` TEXT(200) NOT NULL,
  `driver` TEXT(200) NOT NULL,
  `hostname` TEXT(200) NULL DEFAULT NULL,
  `port` INT(5) NULL DEFAULT NULL,
  `encrypt` TEXT(200) NULL DEFAULT NULL,
  `auth` TEXT(200) NULL DEFAULT NULL,
  `username` TEXT(200) NULL DEFAULT NULL,
  `password` TEXT(500) NULL DEFAULT NULL
);

DROP TABLE IF EXISTS `{DBPREFIX}email_templates`;

CREATE TABLE `{DBPREFIX}email_templates` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `sid` TEXT(200) NOT NULL,
  `active` TEXT(1) CHECK( `active` IN ('1') ) NULL DEFAULT NULL,
  `name` TEXT(200) NOT NULL,
  `subject` TEXT(200) NULL,
  `parent` TEXT(200) NOT NULL,
  `content_txt` TEXT NULL DEFAULT NULL,
  `content_html` TEXT NULL DEFAULT NULL
  UNIQUE(`sid`, `active`)
);
