<?php
class AdminController extends StudipController{
    /**
     * Common code for all actions: set default layout and page title.
     */
    public function before_filter(&$action, &$args) {
        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox');
        $this->set_layout($layout);

        PageLayout::setTitle(_('Benutzerstatistik - Administration'));
        Navigation::activateItem('/benutzerstatistik/admin');
    }

    public function index_action() {
        $config = Config::GetInstance();

        $this->days_to_summarize = DBManager::Get()->query("SELECT COUNT(DISTINCT daystamp) AS quantity FROM user_statistics WHERE daystamp<=LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW())) AND daystamp<=TIMESTAMPADD(DAY, -2, NOW())")->fetchColumn();
        $this->internal_ip_mask = $config['BENUTZERSTATISTIK_INTERNAL_IP_MASK'];
        $this->store_hits       = (bool) $config['BENUTZERSTATISTIK_STORE_HITS'];
        $this->extra_tab        = $config['BENUTZERSTATISTIK_EXTRA_TAB']
                                ? unserialize($config['BENUTZERSTATISTIK_EXTRA_TAB'])
                                : array();

        $this->tracked_urls     = array_map('reset', DBManager::Get()->query("SELECT url_id, url, description, active FROM user_statistics_tracked_urls_config ORDER BY url_id ASC")->fetchAll(PDO::FETCH_GROUP));
    }

    public function config_action() {
        $config = Config::GetInstance();
        try {
            $config->store('BENUTZERSTATISTIK_INTERNAL_IP_MASK', Request::get('internal_ip_mask'));
        } catch (Exception $e) {
            $config->create('BENUTZERSTATISTIK_INTERNAL_IP_MASK', Request::get('internal_ip_mask'));
        }
        try {
            $config->store('BENUTZERSTATISTIK_STORE_HITS', Request::get('store_hits'));
        } catch (Exception $e) {
            $config->create('BENUTZERSTATISTIK_STORE_HITS', Request::get('store_hits'));
        }

        PageLayout::postMessage(MessageBox::success(_('Die Einstellungen wurden erfolgreich gespeichert.')));
        $this->redirect('admin/index');
    }

    public function edit_action() {
        $statement = DBManager::Get()->prepare("INSERT INTO `user_statistics_tracked_urls_config` (`url_id`, `active`, `description`, `url`) VALUES (?, ?, TRIM(?), TRIM(?)) ON DUPLICATE KEY UPDATE `active` = VALUES(`active`), `description` = VALUES(`description`), `url` = VALUES(`url`)");
        foreach (Request::getArray('urls') as $id => $url) {
            $statement->execute(array(
                $id,
                $url['active'],
                $url['description'],
                $url['url']
            ));
        }

        StudipCacheFactory::getCache()
            ->expire(BenutzerStatistik::CACHE_KEY_TRACKED_URLS);

        PageLayout::postMessage(MessageBox::success(_('Die Einstellungen wurden erfolgreich gespeichert.')));
        $this->redirect('admin/index');
    }

    public function add_action() {
        $statement = DBManager::Get()->prepare("INSERT INTO `user_statistics_tracked_urls_config` (`url_id`, `active`, `description`, `url`) VALUES (?, ?, TRIM(?), TRIM(?)) ON DUPLICATE KEY UPDATE `active` = VALUES(`active`), `description` = VALUES(`description`), `url` = VALUES(`url`)");

        $statement->execute(array(
            null,
            Request::int('new_active'),
            Request::get('new_description'),
            Request::get('new_url'),
        ));

        StudipCacheFactory::getCache()
            ->expire(BenutzerStatistik::CACHE_KEY_TRACKED_URLS);

        PageLayout::postMessage(MessageBox::success(_('Die URL wurde erfolgreich eingetragen.')));
        $this->redirect('admin/index');
    }

    public function reset_action($id) {
        $statement = DBManager::get()->prepare("DELETE FROM `user_statistics_tracked_urls` WHERE `url_id` = ?");
        $statement->execute(array($id));

        PageLayout::postMessage(MessageBox::success(_('Die URL wurde erfolgreich zurückgesetzt')));
        $this->redirect('admin/index');
    }

    public function remove_action($id) {
        $statement = DBManager::get()->prepare("DELETE FROM `user_statistics_tracked_urls` WHERE `url_id` = ?");
        $statement->execute(array($id));

        $statement = DBManager::get()->prepare("DELETE FROM `user_statistics_tracked_urls_config` WHERE `url_id` = ?");
        $statement->execute(array($id));

        StudipCacheFactory::getCache()
            ->expire(BenutzerStatistik::CACHE_KEY_TRACKED_URLS);

        PageLayout::postMessage(MessageBox::success(_('Die URL wurde erfolgreich gelöscht')));
        $this->redirect('admin/index');
    }

    public function summarize_action() {
        new BenutzerStatistik_Summarizer();

        PageLayout::postMessage(MessageBox::success(_('Die Daten wurden erfolgreich zusammengefasst.')));
        $this->redirect('admin/index');
    }

    public function extra_tab_action()
    {
        $config = Config::GetInstance();

        $extra_tab = array(
            'title' => Request::get('extra-tab-title'),
            'url'   => Request::get('extra-tab-url'),
        );
        $extra_tab = array_filter($extra_tab);

        if (count($extra_tab) === 2) {
            try {
                $config->store('BENUTZERSTATISTIK_EXTRA_TAB', serialize($extra_tab));
            } catch (Exception $e) {
                $config->create('BENUTZERSTATISTIK_EXTRA_TAB', array('value' => serialize($extra_tab)));
            }
            $message = MessageBox::success(_('Der zusätzliche Tab wurde erfolgreich gespeichert.'));
        } else if (count($extra_tab) === 1) {
            $message = MessageBox::error(_('Bitte geben Sie sowohl einen Titel als auch eine URL für den zusätzlichen Tab an.'));
        } else {
            $config->delete('BENUTZERSTATISTIK_EXTRA_TAB');
            $message = MessageBox::success(_('Der zusätzliche Tab wurde erfolgreich entfernt.'));
        }

        PageLayout::postMessage($message);
        $this->redirect('admin/index');
    }
}
