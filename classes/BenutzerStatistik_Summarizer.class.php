<?php
// +---------------------------------------------------------------------------+
// BenutzerStatistik_Summarizer.class.php
// Part of the StudIP BenutzerStatistik Plugin
//
// Copyright (c) 2008-2010 Jan-Hendrik Willms <tleilax+studip@gmail.com>
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
 * BenutzerStatistik_Summarizer.class.php
 *
 * This class is the aggregator for the user statistics.
 *
 * @author      Jan-Hendrik Willms <tleilax@mindfuck.de>
 * @package     IBIT_StudIP
 * @subpackage  BenutzerStatistik
 * @version     1.0.1
 */
class BenutzerStatistik_Summarizer {
    private $user_agents = null;

    /**
     * Constructor as well as main logic
     */
    public function __construct() {
        $old_state = DBManager::get()->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);

        DBManager::get()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        $this->summarizeScreensizes();
        $this->summarizeOperatingSystems();
        $this->summarizeBrowsers();
        $this->summarizeDaily();
        $this->summarizeMonthly();
        $this->summarizeUsers();
        $this->summarizeUsersMonthly();

        $this->cleanUp();

        DBManager::get()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $old_state);
    }

    /**
     * This function is used to summarize the screen sizes
     */
    private function summarizeScreensizes() {
        $query = "INSERT IGNORE INTO user_statistics_screensizes
                      (width, height, quantity)
                  SELECT screen_width AS width, screen_height AS height, COUNT(*) AS quantity
                  FROM user_statistics
                  WHERE daystamp <= LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW()))
                    AND daystamp <= TIMESTAMPADD(DAY, -2, NOW())
                  GROUP BY screen_width, screen_height
                  ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        DBManager::get()->exec($query);
    }

    /**
     * This function is used to summarize the operating systems
     */
    private function summarizeOperatingSystems() {
        $this->readUserAgents();

        $query = "INSERT IGNORE INTO user_statistics_os (os, quantity)
                  VALUES (?, ?)
                  ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        $statement = DBManager::get()->prepare($query);
        foreach (BenutzerStatistik_Helper::parseOperatingSystems($this->user_agents, array(), 0) as $os) {
            $statement->execute(array($os['name'], $os['quantity']));
        }
    }

    /**
     * This function aggregates is used to summarize the used browsers
     */
    private function summarizeBrowsers() {
        $this->readUserAgents();

        $values = array();
        foreach (BenutzerStatistik_Helper::parseUserAgentsForSummary($this->user_agents) as $user_agent) {
            if ($user_agent['name'] == _('unbekannt')) {
                foreach ($user_agent['versions'] as $version=>$info) {
                    $values[] = sprintf("(MD5('%s'), NULL, NULL, '%s', %d)",
                        $info['user_agent'],
                        $info['user_agent'],
                        $info['quantity']
                    );
                }
            } else {
                foreach ($user_agent['versions'] as $version=>$info) {
                    $values[] = sprintf("(MD5('%s'), '%s', '%s', '%s', %d)",
                        $info['user_agent'],
                        $user_agent['name'],
                        $version,
                        $info['user_agent'],
                        $info['quantity']
                    );
                }
            }
        }
        if (!empty($values)) {
            DBManager::get()->exec("INSERT IGNORE INTO user_statistics_browser (agent_hash, browser, version, user_agent, quantity) VALUES ".implode(',', $values)." ON DUPLICATE KEY UPDATE quantity=quantity+VALUES(quantity)");
        }
    }

    /**
     * Summarizes data from statistics table into another table containing summarized daily data.
     */
    private function summarizeDaily() {
        DBManager::Get()->exec("INSERT IGNORE INTO user_statistics_daily (daystamp, permission, visits, unique_visits, hits, internal, ajax, mobile, tablet) SELECT daystamp, permission, COUNT(*) AS visits, COUNT(DISTINCT hash) AS unique_visits, SUM(hits) AS hits, SUM(internal) AS internal, SUM(ajax) AS ajax, SUM(mobile) AS mobile, SUM(tablet) AS tablet FROM user_statistics WHERE daystamp<=LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW())) AND daystamp<=TIMESTAMPADD(DAY, -2, NOW()) GROUP BY daystamp, permission");
    }

    /**
     * Summarizes data from statistics table into another table containing summarized monthly data.
     */
    private function summarizeMonthly() {
        DBManager::Get()->exec("INSERT IGNORE INTO user_statistics_monthly (month_stamp, permission, visits, unique_visits, hits, internal, ajax, javascript, mobile, tablet) (SELECT LAST_DAY(daystamp) AS month_stamp, permission, COUNT(*) AS visits, COUNT(DISTINCT hash) AS unique_visits, SUM(hits) AS hits, SUM(internal) AS internal, SUM(ajax) AS ajax, SUM(javascript) AS javascript, SUM(mobile) AS mobile, SUM(tablet) AS tablet FROM user_statistics WHERE daystamp<=LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW())) AND daystamp<=TIMESTAMPADD(DAY, -2, NOW()) GROUP BY LAST_DAY(daystamp), permission) ON DUPLICATE KEY UPDATE visits=visits+VALUES(visits), unique_visits=unique_visits+VALUES(unique_visits), hits=hits+VALUES(hits), internal=internal+VALUES(internal), ajax = ajax + VALUES(ajax), javascript=javascript+VALUES(javascript), mobile = mobile + VALUES(mobile), tablet = tablet + VALUES(tablet)");
    }

    /**
     * Summarizes data about the uniqueness of users into another table.
     */
    private function summarizeUsers() {
        DBManager::Get()->exec("INSERT IGNORE INTO user_statistics_uniqueusers (hash, permission, hits, visits) (SELECT hash, permission, SUM(hits) AS hits, COUNT(*) AS visits FROM user_statistics WHERE daystamp<=LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW())) AND daystamp<=TIMESTAMPADD(DAY, -2, NOW()) GROUP BY hash) ON DUPLICATE KEY UPDATE hits=hits+VALUES(hits), visits=visits+VALUES(visits)");
    }

    /**
     * Summarizes data about quarterly unique users.
     */
    private function summarizeUsersMonthly() {
        $query = "INSERT IGNORE INTO user_statistics_uniqueusers_monthly
                     (hash, permission, month, year)
                  SELECT DISTINCT hash, permission, MONTH(daystamp) AS month,
                      YEAR(daystamp) AS year
                  FROM user_statistics
                  WHERE daystamp <= LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW()))
                    AND daystamp<=TIMESTAMPADD(DAY, -2, NOW())";
        DBManager::Get()->exec($query);
    }

    /**
     * Reads all required user agents from database
     */
    private function readUserAgents() {
        if (!is_null($this->user_agents)) {
            return;
        }

        $query = "SELECT user_agent, COUNT(*) AS quantity
                  FROM user_statistics
                  WHERE daystamp <= LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW()))
                    AND daystamp <= TIMESTAMPADD(DAY, -2, NOW())
                  GROUP BY user_agent";
        $this->user_agents = DBManager::Get()
            ->query($query)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * This function removes all summarized from the statistics table and optimizes the table
     */
    private function cleanUp() {
        $query = "DELETE FROM user_statistics
                  WHERE daystamp <= LAST_DAY(TIMESTAMPADD(MONTH, -1, NOW()))
                    AND daystamp <= TIMESTAMPADD(DAY, -2, NOW())";
        DBManager::Get()->exec($query);
    }
}
