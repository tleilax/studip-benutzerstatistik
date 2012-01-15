<?php
class AddInternalFlag extends DBMigration
{
	function up()
	{
		if (!DBManager::Get()->query("SHOW COLUMNS FROM user_statistics LIKE 'internal'")->fetch(PDO::FETCH_COLUMN))
			DBManager::Get()->exec("ALTER IGNORE TABLE user_statistics ADD COLUMN internal TINYINT(1) UNSIGNED DEFAULT 0 AFTER permission");
		if (!DBManager::Get()->query("SHOW COLUMNS FROM user_statistics_daily LIKE 'internal'")->fetch(PDO::FETCH_COLUMN))
			DBManager::Get()->exec("ALTER IGNORE TABLE user_statistics_daily ADD COLUMN internal TINYINT(1) UNSIGNED DEFAULT 0 AFTER hits");
		if (!DBManager::Get()->query("SHOW COLUMNS FROM user_statistics_monthly LIKE 'internal'")->fetch(PDO::FETCH_COLUMN))
			DBManager::Get()->exec("ALTER IGNORE TABLE user_statistics_monthly ADD COLUMN internal TINYINT(1) UNSIGNED DEFAULT 0 AFTER hits");
	}

	function down()
	{
/*
		DBManager::Get()->exec("ALTER TABLE user_statistics DROP COLUMN internal");
		DBManager::Get()->exec("ALTER TABLE user_statistics_daily DROP COLUMN internal");
		DBManager::Get()->exec("ALTER TABLE user_statistics_monthly DROP COLUMN internal");
*/
	}
}
