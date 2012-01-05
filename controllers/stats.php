<?php
class StatsController extends StudipController
{
    /**
     * Common code for all actions: set default layout and page title.
     */
    function before_filter(&$action, &$args) {
        $this->flash = Trails_Flash::instance();
        $this->plugin = $this->dispatcher->plugin;
        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox');
        $this->set_layout($layout);

        PageLayout::setTitle('Benutzerstatistik - Statistiken');
        Navigation::activateItem('/benutzerstatistik/stats/'.$action);

        $this->icon_path = $this->plugin->getPluginURL().'/assets/images/';
        $this->show_hits = Config::GetInstance()->getValue('BENUTZERSTATISTIK_STORE_HITS');

        $this->os_mapping = array(
            'Android'        => 'android',
            'BlackBerry'     => 'blackberry',
            'iOS'            => 'ios',
            'Linux'		     => 'linux',
            'Macintosh'      => 'macosx',
            'Windows'	     => 'windows',
            'Windows Mobile' => 'windowsmobile',
        );

        PageLayout::addScript($this->plugin->getPluginURL().'/assets/vendor/jquery.flot.min.js');
        PageLayout::addScript($this->plugin->getPluginURL().'/assets/vendor/jquery.flot.pie.min.js');
        $GLOBALS['_include_additional_header'] .= '<!--[if lt IE 9]><script src="'.$this->plugin->getPluginURL().'/assets/vendor/excanvas.min.js" type="text/javascript"></script><![endif]-->';
    }

    function index_action() {
        $this->stats = BenutzerStatistik_Helper::getStatistics(time());
    }

    function yesterday_action() {
        $this->stats = BenutzerStatistik_Helper::getStatistics(strtotime('yesterday'));
        $this->render_action('index');
    }

    function total_action() {
        $this->stats = BenutzerStatistik_Helper::getStatistics();
        $this->render_action('index');
    }

    function system_action () {
        $data = array_map('reset', DBManager::get()->query("SELECT IFNULL(perms, 'total'), COUNT(*) AS total, SUM(IF(changed > NOW() - INTERVAL 4 WEEK, 1, 0)) AS active FROM user_data JOIN auth_user_md5 ON (sid = user_id) GROUP BY perms WITH ROLLUP")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));

        $this->data = $data;
    }
}
