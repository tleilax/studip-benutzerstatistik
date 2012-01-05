<?php
class Aggregate extends DBMigration
{
	function up()
	{
		// Add table for operating systems
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_os (os VARCHAR(16) NOT NULL DEFAULT '?', quantity BIGINT(20) NOT NULL DEFAULT 1, PRIMARY KEY (os))");

		// Add table for screen sizes
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_screensizes (width INT(4) NOT NULL DEFAULT 0, height INT(4) NOT NULL DEFAULT 0, quantity BIGINT(20) NOT NULL DEFAULT 1, PRIMARY KEY (width, height))");

		// Add table for browsers
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_browser (agent_hash CHAR(32) NOT NULL DEFAULT '', browser VARCHAR(64) NULL DEFAULT NULL, version VARCHAR(32) NULL DEFAULT NULL, user_agent VARCHAR(255) NOT NULL DEFAULT '', quantity BIGINT(20) NOT NULL DEFAULT 1, PRIMARY KEY (agent_hash))");

		// Add table for daily summary
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_daily (daystamp DATE NOT NULL, permission SET('unknown', 'student', 'teacher', 'admin') NOT NULL DEFAULT 'unknown', visits BIGINT(20) NOT NULL DEFAULT 0, unique_visits BIGINT(20) NOT NULL DEFAULT 0, hits BIGINT(20) NOT NULL DEFAULT 0, PRIMARY KEY (daystamp, permission))");

		// Add table for monthly summary
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_monthly (month_stamp DATE NOT NULL, permission SET('unknown', 'student', 'teacher', 'admin') NOT NULL DEFAULT 'unknown', visits BIGINT(20) NOT NULL DEFAULT 0, unique_visits BIGINT(20) NOT NULL DEFAULT 0, hits BIGINT(20) NOT NULL DEFAULT 0, javascript BIGINT(20) NOT NULL DEFAULT 0, PRIMARY KEY (month_stamp, permission))");

		// Add table for unique user data and fill with 'dummy' values
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_uniqueusers (hash CHAR(32) NOT NULL DEFAULT '', permission SET('unknown', 'student', 'teacher', 'admin') NOT NULL DEFAULT 'unknown', visits BIGINT(20) NOT NULL DEFAULT 1, hits BIGINT(20) NOT NULL DEFAULT 1, PRIMARY KEY (hash)) SELECT hash, permission, 0 AS visits, 0 as hits FROM user_statistics GROUP BY hash");

		// Add table for quarterly unique user data
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_uniqueusers_monthly (hash CHAR(32) NOT NULL DEFAULT '', permission SET('unknown', 'student', 'teacher', 'admin') NOT NULL DEFAULT 'unknown', month TINYINT(2) NOT NULL DEFAULT 0, year INT(4) NOT NULL DEFAULT 0, PRIMARY KEY (hash, permission, month, year))");
	}

	function down()
	{
		// Drop tables
		DBManager::Get()->exec("DROP TABLE IF EXISTS user_statistics_browser, user_statistics_daily, user_statistics_monthly, user_statistics_os, user_statistics_screensizes, user_statistics_uniqueusers, user_statistics_uniqueusers_monthly");
	}
}

