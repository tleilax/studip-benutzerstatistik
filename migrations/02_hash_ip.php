<?php
class HashIp extends DBMigration {

	function description () {
		return '';
	}

	function up() {
		// Hash IP as MD5
		DBManager::Get()->exec("ALTER TABLE user_statistics ADD COLUMN tmp_ip CHAR(32) NOT NULL DEFAULT ''");
		DBManager::Get()->exec("UPDATE user_statistics SET tmp_ip=MD5(ip)");
		DBManager::Get()->exec("ALTER TABLE user_statistics DROP PRIMARY KEY, DROP COLUMN ip, CHANGE COLUMN tmp_ip ip CHAR(32) NOT NULL DEFAULT '' FIRST, ADD PRIMARY KEY (ip, user_agent, daystamp, hash)");

		// Add index for daystamp
		DBManager::Get()->exec("ALTER TABLE user_statistics ADD KEY daystamp (daystamp)");

		// Optimize
		DBManager::Get()->exec("OPTIMIZE TABLE user_statistics");
	}

	function down() {
		// Drop index for daystamp
		DBManager::Get()->exec("ALTER TABLE user_statistics DROP KEY daystamp");

		// Hashing is not reversible

		// Optimize
		DBManager::Get()->exec("OPTIMIZE TABLE user_statistics");
	}
}
