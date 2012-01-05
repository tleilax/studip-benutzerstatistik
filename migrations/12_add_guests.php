<?php
class AddGuests extends DBMigration {
    private $tables = array(
        'user_statistics',
        'user_statistics_daily',
        'user_statistics_monthly',
        'user_statistics_uniqueusers',
        'user_statistics_uniqueusers_monthly',
    );
    
    public function description () {
        return 'Fügt die Möglichkeit Gäste zu erkennen hinzu';
    }
    
    public function up () {
        $db = DBManager::get();
        
        foreach ($this->tables as $table) {
            // Add guest option
            $db->exec("ALTER TABLE `{$table}` MODIFY COLUMN `permission` ENUM('unknown','guest','student','teacher','admin') NOT NULL DEFAULT 'unknown'");            
        }
    }
    
    public function down() {
        $db = DBManager::get();
        
        foreach ($this->tables as $table) {
            // Rewrite all guests to students
            $db->exec("UPDATE `{$table}` SET `permission` = 'student' WHERE `permission` = 'guest'");
            // Remove guest option
            $db->exec("ALTER TABLE `{$table}` MODIFY COLUMN `permission` SET('unknown','student','teacher','admin') NOT NULL DEFAULT 'unknown'");            
        }
    }
}
