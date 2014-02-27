<?php
class BenutzerstatistikCronjob extends CronJob
{
    public static function getName()
    {
        return _('Benutzerstatistik-Cronjob');
    }

    public static function getDescription()
    {
        return _('Cronjob für die Benutzerstatistik, der die Statistiken monatlich zusammenfasst');
    }

    public function setUp()
    {
        require 'BenutzerStatistik_Summarizer.class.php';
        require 'BenutzerStatistik_Helper.class.php';
    }

    public function execute($last_result, $parameters = array())
    {
        new BenutzerStatistik_Summarizer();
    }
}
