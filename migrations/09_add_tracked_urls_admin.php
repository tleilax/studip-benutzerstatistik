<?php
class AddTrackedUrlsAdmin extends DBMigration
{
	function up()
	{
		DBManager::Get()->exec("CREATE TABLE IF NOT EXISTS `user_statistics_tracked_urls_config` (`url_id` INT NOT NULL AUTO_INCREMENT, `url` VARCHAR(255) NOT NULL, `description` VARCHAR(255) NOT NULL, `active` ENUM('0','1') NOT NULL DEFAULT 0, PRIMARY KEY (`url_id`))");

		$config = Config::GetInstance();
		$url = $config->getValue('BENUTZERSTATISTIK_TRACKED_URL');

		$statement = DBManager::Get()->prepare("INSERT IGNORE INTO `user_statistics_tracked_urls_config` (`url_id`, `url`, `description`, `active`) VALUES (1, ?, 'Webmail-Aufrufe', '1')");
		$statement->execute(array($url));

		$config->delete('BENUTZERSTATISTIK_TRACKED_URL');

		// Add url_id to tracking table
		DBManager::Get()->exec("ALTER IGNORE TABLE `user_statistics_tracked_urls` ADD COLUMN `url_id` INT NOT NULL DEFAULT 1 FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY  USING BTREE(`user_hash`, `daystamp`, `url_id`);");
	}

	function down()
	{
	}
}

