<?php
class AddMobile extends DBMigration {

    function description () {
        return 'Fügt die Möglichkeit hinzu, mobile Geräte zu erkennen.';
    }

    function up() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics ADD COLUMN mobile TINYINT(1) NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_daily ADD COLUMN mobile BIGINT(20) NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_monthly ADD COLUMN mobile BIGINT(20) NOT NULL DEFAULT 0");
    }

    function down() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics DROP COLUMN mobile");
        $db->exec("ALTER TABLE user_statistics_daily DROP COLUMN mobile");
        $db->exec("ALTER TABLE user_statistics_monthly DROP COLUMN mobile");
    }
}
