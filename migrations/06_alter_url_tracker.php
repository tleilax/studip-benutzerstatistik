<?php
class AlterUrlTracker extends DBMigration {

	function up() {
		DBManager::get()->exec("TRUNCATE TABLE user_statistics_tracked_urls");

		if (DBManager::Get()->query("SHOW COLUMNS FROM user_statistics_tracked_urls LIKE 'url'")->fetch(PDO::FETCH_COLUMN))
			DBManager::get()->exec("ALTER IGNORE TABLE user_statistics_tracked_urls DROP COLUMN url");
		if (!DBManager::Get()->query("SHOW COLUMNS FROM user_statistics_tracked_urls LIKE 'user_hash'")->fetch(PDO::FETCH_COLUMN))
			DBManager::get()->exec("ALTER IGNORE TABLE user_statistics_tracked_urls ADD COLUMN user_hash CHAR(32) NOT NULL FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (user_hash, daystamp)");

		$config = new Config();
		$config->setValue('https://webmail.uni-oldenburg.de/cas', 'BENUTZERSTATISTIK_TRACKED_URL');
	}

	function down() {
		// No use to downgrade, up() did some irreversible changes.
		// In other, maybe simpler words: DON'T EVER DO IT!!
		// On the other hand why would you even want to downgrade?
	}
}