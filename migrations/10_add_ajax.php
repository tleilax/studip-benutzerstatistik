<?php
class AddAjax extends DBMigration {

    function description () {
        return 'Fügt die Möglichkeit hinzu, AJAX-Aufrufe zu ermitteln und passt das Internal-Flag aus Migration 4 an.';
    }

    function up() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics ADD COLUMN ajax INT(11) UNSIGNED NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_daily ADD COLUMN ajax BIGINT(20) UNSIGNED NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_monthly ADD COLUMN ajax BIGINT(20) UNSIGNED NOT NULL DEFAULT 0");
        
        // Repair migration #4
        $db->exec("ALTER TABLE user_statistics_daily MODIFY COLUMN internal BIGINT(20) UNSIGNED NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_monthly MODIFY COLUMN internal BIGINT(20) UNSIGNED NOT NULL DEFAULT 0");
    }

    function down() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics DROP COLUMN ajax");
        $db->exec("ALTER TABLE user_statistics_daily DROP COLUMN ajax");
        $db->exec("ALTER TABLE user_statistics_monthly DROP COLUMN ajax");
    }
}
