<?php
# TODO: http://code.google.com/p/flot/

// +---------------------------------------------------------------------------+
// BenutzerStatistik_Plugin.class.php
// Part of the StudIP BenutzerStatistik Plugin
//
// Copyright (c) 2008-2012 Jan-Hendrik Willms <tleilax+studip@gmail.com>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+/


/**
 * BenutzerStatistikPlugin.class.php
 *
 * @author      Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @package     IBIT_StudIP
 * @subpackage  BenutzerStatistik
 * @version     2.5
 */

require_once 'bootstrap.php';

class BenutzerStatistik extends StudipPlugin implements SystemPlugin {

    const CACHE_KEY_TRACKED_URLS = '/benutzerstatistik/tracked_urls';
    const CACHE_DURATION = 3600;

    private static $permission_mappings = array(
        'root'   => 'admin',
        'admin'  => 'admin',
        'dozent' => 'teacher',
        'tutor'  => 'student',
        'autor'  => 'guest',
    );

    private $config;
    private $hash;

    /**
     * Initialize a new instance of the plugin.
     */
    public function __construct() {
        parent::__construct();

        if ($GLOBALS['auth']->auth['perm'] === 'root') {
            // set up top navigation
            $navigation = new AutoNavigation('Benutzerstatistik');
            $navigation->setURL(PluginEngine::getURL('benutzerstatistik', array(), 'stats'));
            $navigation->setImage($this->getPluginURL().'/assets/images/statistics.png');
            Navigation::addItem('/benutzerstatistik', $navigation);
        }

        static $calls = 0;
        if (($calls++ > 0) or !$this->get_hash()) {
            return;
        }

        $this->inject_js();
        $this->store_hit();
    }

    public function initialize() {
        if ($GLOBALS['auth']->auth['perm'] !== 'root') {
            return;
        }

        $css_file = $this->getPluginPath().'/assets/benutzerstatistik.css';
        if (!file_exists($css_file)) {
            $css = $this->render('templates/css.php', array(
                'path' => $this->getPluginURL().'/assets/images/',
            ));
            file_put_contents($css_file, $css);
        }

        PageLayout::addStylesheet($this->getPluginURL().'/assets/benutzerstatistik.css');
        PageLayout::addScript($this->getPluginURL().'/assets/benutzerstatistik.js');

        // set up tab navigation
        $tabs = array(
            'stats'             => _('Statistiken'),
            'stats/index'       => _('Heute'),
            'stats/yesterday'   => _('Gestern'),
            'stats/total'       => _('Gesamt'),
#            'stats/system'      => _('Systemweit'),
            'graphs'            => _('Graphen'),
            'graphs/index'      => _('Monatlich'),
            'graphs/quarterly'  => _('Quartalsweise'),
            'graphs/uni_yearly' => _('Uni-Jährlich'),
            'graphs/yearly'     => _('Jährlich'),
            'admin'             => _('Administration'),
        );

        $config = Config::GetInstance();
        if ($config['BENUTZERSTATISTIK_EXTRA_TAB']) {
            $extra_tab = unserialize($config['BENUTZERSTATISTIK_EXTRA_TAB']);
            $tabs['extra'] = $extra_tab['title'];
        }

        foreach ($tabs as $key => $name) {
            $navigation = new AutoNavigation($name);
            $navigation->setURL(PluginEngine::getURL('benutzerstatistik/'.$key));
            Navigation::addItem('/benutzerstatistik/'.$key, $navigation);
        }

        if (!count($this->get_tracked_urls())) {
            return;
        }

        foreach ($this->get_tracked_urls() as $id => $url) {
            $navigation = new AutoNavigation($url['description']);
            $navigation->setURL(PluginEngine::getURL('benutzerstatistik/graphs/tracked/'.$id));
            Navigation::addItem('/benutzerstatistik/graphs/tracked-'.$id, $navigation);
        }
    }

    private function get_hash() {
        $valid = true;

        $valid = ($valid and !empty($GLOBALS['auth']));
        $valid = ($valid and !empty($GLOBALS['auth']->auth['uid']));
        $valid = ($valid and $GLOBALS['auth']->auth['uid'] !== 'nobody');

        if (!$valid) {
            return false;
        }

        $this->hash = md5($GLOBALS['auth']->auth['uid'].$GLOBALS['auth']->auth['uname']);
        return true;
    }

    private function inject_js() {
        $js = '';

        $urls = $this->get_tracked_urls();
        if (!empty($urls)) {
            $urls = array_map('reset', $urls); // A bit hackish, url is 1st element
            $js .= $this->render('assets/injector.js', compact('urls'));
        }

        if (!$_SESSION['BENUTZERSTATISTIK']['js'] and $_SESSION['BENUTZERSTATISTIK']['hits'] <= 3) {
            $js .= $this->render('assets/sniffer.js');
        }

        if (!empty($js)) {
            $js = $this->render('assets/wrapper.js', compact('js'));
            PageLayout::addHeadElement('script', array('type' => 'text/javascript'), $js);
        }
    }

    private function get_tracked_urls($cached = true) {
        $cache = StudipCacheFactory::getCache();
        if ($cached and $urls = $cache->read(self::CACHE_KEY_TRACKED_URLS)) {
            return unserialize($urls);
        }

        $result = DBManager::Get()
            ->query("SELECT `url_id`, `url`, `description` FROM `user_statistics_tracked_urls_config` WHERE `active` = '1'")
            ->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
        $result = array_map('reset', $result);
        $cache->write(self::CACHE_KEY_TRACKED_URLS, serialize($result), self::CACHE_DURATION);

        return $result;
    }

    private function store_hit() {
        $this->config = Config::GetInstance();

        // Reinit if session started yesterday
        if (isset($_SESSION['BENUTZERSTATISTIK'])
            and ($_SESSION['BENUTZERSTATISTIK']['hash'] !== date('Ymd'))) {
            unset($_SESSION['BENUTZERSTATISTIK']);
        }

        // Init on first hit
        if (empty($_SESSION['BENUTZERSTATISTIK'])) {
            $_SESSION['BENUTZERSTATISTIK'] = array(
                'hash' => date('Ymd'),
                'hits' => 0,
                'js'   => false,
            );

            // Dummy entry for statistic purposes
            $statement = DBManager::Get()->prepare("INSERT DELAYED IGNORE INTO user_statistics_uniqueusers (hash, permission, visits, hits) VALUES (?, ?, 0, 0)");
            $statement->execute(array(
                $this->hash,
                $permission,
            ));
            $statement->closeCursor();
        } elseif (!$this->config->getValue('BENUTZERSTATISTIK_STORE_HITS')) {
            // Leave if we have a reoccuring hit and don't want to store atomic hits
            return;
        }

        $_SESSION['BENUTZERSTATISTIK']['hits'] += 1;

        if (!$user_agent = @$_SERVER['HTTP_USER_AGENT']) {
            $user_agent = '?';
        }

        $permission = @self::$permission_mappings[ $GLOBALS['auth']->auth['perm'] ];
        if (!$permission) {
            log_event('LOG_ERROR', null, null, 'BENUTZERSTATISTIK: user-id='.@$GLOBALS['auth']->auth['uid'].', perms='.$GLOBALS['auth']->auth['perm']);
            $permission = 'unknown';
        }

        $query = <<<QUERY
INSERT DELAYED INTO user_statistics (ip, user_agent, daystamp, hash, permission, internal, ajax, mobile, tablet)
VALUES (MD5(INET_ATON(?)), ?, CURDATE(), TRIM(?), ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE hits = hits + 1, ajax = ajax + VALUES(ajax)
QUERY;
        $statement = DBManager::Get()->prepare($query);
        $statement->execute(array(
            $_SERVER['REMOTE_ADDR'],
            $user_agent,
            $this->hash,
            $permission,
            (int)fnmatch($this->config->getValue('BENUTZERSTATISTIK_INTERNAL_IP_MASK'), $_SERVER['REMOTE_ADDR']),
            (int)(@$_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'),
            (int)(mobile_device_detect() !== false),
            (int)$this->isTabletDevice(),
        ));
        $statement->closeCursor();
    }

    private function isTabletDevice () {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        return (strpos($agent, 'ipad') !== false)
            or (strpos($agent, 'android') !== false
                and strpos($agent, 'mobile') === false);
    }

    private function render($template, $variables = array()) {
        extract($variables);

        ob_start();
        include $template;
        return ob_get_clean();
    }

    /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     */
    public function perform($unconsumed_path) {
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath(),
            rtrim(str_replace('?cid=', '', PluginEngine::GetLink($this, array(), null)), '/'),
            'stats'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }
}
