<?php
class AddCronjob extends DBMigration
{
    function description ()
    {
        return 'Fügt den Cronjob zum monatlichen Zusammenfassen der Statistiken hinzu.';
    }

    function up()
    {
        $task_id = CronjobScheduler::registerTask($this->getCronjobFilename());
        $schedule = CronjobScheduler::schedulePeriodic($task_id, 0, 0, 2);

        $schedule->active = true;
        $schedule->store();
    }

    function down()
    {
        $task_id = CronjobTask::findByFilename($this->getCronjobFilename())->task_id;
        CronjobScheduler::unregisterTask($task_id);
    }

    private function getCronjobFilename()
    {
        return str_replace($GLOBALS['STUDIP_BASE_PATH'] . '/', '',
                           realpath(__DIR__ . '/../classes/Benutzerstatistik_Cronjob.class.php'));
    }
}
