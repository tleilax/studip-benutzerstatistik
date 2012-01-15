<?php
class AddTrackedUrls extends DBMigration
{
	function up()
	{
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS user_statistics_tracked_urls (url VARCHAR(255) NOT NULL, daystamp DATE NOT NULL, clicks BIGINT(20) NOT NULL DEFAULT 1, PRIMARY KEY (url, daystamp))");
	}

	function down()
	{
/*
		DBManager::Get()->exec("DROP TABLE user_statistics_tracked_urls");
*/
	}
}
