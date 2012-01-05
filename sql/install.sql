CREATE TABLE IF NOT EXISTS `user_statistics` (
	`ip` INT(11) UNSIGNED NOT NULL,
	`user_agent` VARCHAR(255) NOT NULL DEFAULT '',
	`daystamp` DATE NOT NULL,
	`hash` CHAR(32) NOT NULL DEFAULT '',
	`hits` INT(11) UNSIGNED NOT NULL DEFAULT 1,
	`screen_width` INT(4) NULL DEFAULT NULL,
	`screen_height` INT(4) NULL DEFAULT NULL,
	`javascript` TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`ip`, `user_agent`, `daystamp`, `hash`)
);