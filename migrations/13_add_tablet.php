<?php
class AddTablet extends DBMigration {
    
    function description () {
        return 'Fügt die Möglichkeit hinzu, Tabletgeräte zu erkennen.';
    }

    function up() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics ADD COLUMN tablet TINYINT(1) NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_daily ADD COLUMN tablet BIGINT(20) NOT NULL DEFAULT 0");
        $db->exec("ALTER TABLE user_statistics_monthly ADD COLUMN tablet BIGINT(20) NOT NULL DEFAULT 0");
    }

    function down() {
        $db = DBManager::Get();
        
        $db->exec("ALTER TABLE user_statistics DROP COLUMN tablet");
        $db->exec("ALTER TABLE user_statistics_daily DROP COLUMN tablet");
        $db->exec("ALTER TABLE user_statistics_monthly DROP COLUMN tablet");
    }
}
