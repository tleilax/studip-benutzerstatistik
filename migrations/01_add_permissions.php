<?php
class AddPermissions extends DBMigration {

	function description () {
		return '';
	}

	function up () {
		$db = DBManager::get();
		
		// Add index on hash column
		$db->exec("ALTER IGNORE TABLE `user_statistics` ADD INDEX `hash` (`hash`)");
		// Add permission column
		$db->exec("ALTER TABLE `user_statistics` ADD COLUMN `permission` SET('unknown', 'student', 'teacher', 'admin') NOT NULL DEFAULT 'unknown'");

		// Reconstruct permissions from hashes (may take a while)
		$permissions = array(
			'root'   => 'admin',
			'admin'  => 'admin',
			'dozent' => 'teacher',
			'tutor'  => 'student',
			'autor'  => 'student',
			'user'   => 'student'
		);

		$ids = $db->query("SELECT DISTINCT `hash` FROM `user_statistics` WHERE `permission` = 'unknown'")->fetchAll(PDO::FETCH_COLUMN);
		$data = array_fill_keys($ids, 'unknown');

		$rows = $db->query("SELECT `perms`, `hash` FROM (SELECT `perms`, MD5(CONCAT(`user_id`, `username`)) AS `hash` FROM `auth_user_md5`) AS `tmp_user` WHERE `hash` IN('".implode("','", array_keys($data))."')")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			$data[ $row['hash'] ] = isset($permissions[ $row['perms'] ]) ? $permissions[ $row['perms'] ] : 'unknown';
		}

		$statement = $db->prepare("UPDATE `user_statistics` SET `permission` = ? WHERE `hash` = ?");
		foreach ($data as $hash => $permission) {
			if ($permission === 'unknown') {
				continue;
			}
			$statement->execute(array($permission, $hash));
		}

		// Optimize table
		$db->exec("OPTIMIZE table user_statistics");
	}

	function down () {
		DBManager::Get()->exec("ALTER TABLE `user_statistics` DROP COLUMN `permission`, DROP INDEX `hash`");
	}
}
