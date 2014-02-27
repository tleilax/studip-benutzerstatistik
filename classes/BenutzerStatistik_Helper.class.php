<?php
// +---------------------------------------------------------------------------+
// BenutzerStatistik_Helper.class.php
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
 * BenutzerStatistik_Helper.class.php
 *
 * It's just a helperclass containing various methods.
 *
 * @author      Jan-Hendrik Willms <tleilax@mindfuck.de>
 * @package     IBIT_StudIP
 * @subpackage  BenutzerStatistik
 * @version     1.1
 */

class BenutzerStatistik_Helper {

    /**
     * This function parses a given list of user_agents and returns a list of all
     * used operating systems including their quantity.
     *
     * @param array $user_agents The list of user_agents as an array [<name>, <quantity>]{0..x}
     * @param integer $slice Tells the function to only use a slice of the result (and to order the result in descending order)
     * @return array The list of used operating systems as an array [<name>, <quantity>]{0..x}
     * @static
     */
    public static function parseOperatingSystems($user_agents, $result = array(), $slice = 10) {
        $possible_os = array(
        // Mobile
            'Android'       => 'Android',
            'BlackBerry'    => 'BlackBerry',
            'iPad'          => 'iOS',
            'iPhone'        => 'iOS',
            'iPod'          => 'iOS',
            'IEMobile'      => 'Windows Mobile',
            'Windows CE'    => 'Windows Mobile',
            'Windows Phone' => 'Windows Mobile',
        // Desktop
            'Windows'       => 'Windows',
            'Mac OS X'      => 'Macintosh',
            'Macintosh'     => 'Macintosh',
            'X11'           => 'Linux',
        );

        foreach ($user_agents as $user_agent) {
            $quantity = $user_agent['quantity'];
            $os = _('unbekannt');
            foreach ($possible_os as $key => $name) {
                if (stripos($user_agent['user_agent'], $key) !== false) {
                    $os = $name;
                    break;
                }
            }

            if (empty($result[$os])) {
                $result[$os] = array(
                    'name'     => $os,
                    'quantity' => 0,
                );
            }
            $result[$os]['quantity'] += $quantity;
        }

        usort($result, create_function('$a,$b', 'return $b["quantity"] - $a["quantity"];'));

        if ($slice) {
            $result = array_slice($result, 0, $slice);
        }

        return $result;
    }

    /**
     *
     * This function parses a given list of user_agents and returns a list of all
     * used user agents including their version and quantity.
     *
     * @param array $user_agents The list of user_agents as an array [<name>, <quantity>]{0..x}
     * @param integer $slice Tells the function to only use a slice of the result (and to order the result in descending order)
     * @return array The list of used user agents as an array [<name>, [<version>, <quantity>]{1..x}]{0..x}
     * @static
     */
    public static function parseUserAgents($user_agents, $result = array(), $slice = 10) {
        foreach ($user_agents as $user_agent) {
            $quantity = $user_agent['quantity'];
            $agent = $user_agent['user_agent'];
            $version = '?';
            if (preg_match('~Chrome/(\S+)~i', $agent, $matches)) {
                $agent = 'Chrome';
                $version = $matches[1];
            } elseif (preg_match('~(\d+(?:\.\d+)*)\s+(Safari)~i', $agent, $matches)) {
                $agent = $matches[2];
                $version = $matches[1];
            } elseif (preg_match('~(Epiphany|Chrome|Camino|Safari|SeaMonkey|IceWeasel|Konqueror|Netscape|Opera|Firefox|MSIE)[ /]([^;\s]+)(?:;|\s|$)~iS', $agent, $matches)) {
                $agent = $matches[1];
                $version = $matches[2];
            } elseif (preg_match('~(Epiphany|Chrome|Camino|Safari|SeaMonkey|IceWeasel|Konqueror|Netscape|Opera|Firefox|MSIE)~iS', $agent, $matches)) {
                $agent = $matches[1];
                $version = '?';
            } elseif (preg_match('/(?:GranParadiso|Shiretoko|Namoroka|BonEcho|Minefield)[ \/]([^;\s]+)(?:;|\s|$)/iS', $agent, $matches)) {
                $agent = 'Firefox';
                $version = $matches[1];
            } elseif (preg_match('~Dolfin/(\S+)~', $agent, $matches)) {
                $agent = 'Dolphin';
                $version = $matches[1];
            } elseif (preg_match('~(Nokia|SonyEricsson|iPhone|iPad|BlackBerry|\bLG)~', $agent, $matches)) {
                $agent = 'Mobile';
                $version = $matches[1];
            } elseif (preg_match('~Windows.*Trident.*rv:(\d+\.\d+)~', $agent, $matches)) {
                $agent   = 'MSIE';
                $version = $matches[1];
            } else {
                $version = $agent;
                $agent = _('unbekannt');
            }

            $hash = md5($agent);
            if (!empty($result[$hash])) {
                $result[$hash]['quantity'] += $quantity;
                if (empty($result[$hash]['versions'][$version])) {
                    $result[$hash]['versions'][$version] = 0;
                }
                $result[$hash]['versions'][$version] += $quantity;
            } else {
                $result[$hash] = array(
                    'name'     => $agent,
                    'quantity' => $quantity,
                    'versions' => array(
                        $version => $quantity
                    ),
                );
            }
        }

        foreach ($result as $index => $item) {
            uasort($item['versions'], create_function('$a, $b', 'return $b - $a;'));
            $result[$index] = $item;
        }
        usort($result, create_function('$a, $b', 'return $b["quantity"] - $a["quantity"];'));

        if ($slice) {
            $result = array_slice($result, 0, $slice);
        }

        return $result;
    }

    /**
     * This function parses a given list of user_agents and returns a list of all
     * used user agents including their version and quantity.
     *
     * Specialized version of the above function for aggregation purposes.
     *
     * @param array $user_agents The list of user_agents as an array [<name>, <quantity>]{0..x}
     * @return array The list of used user agents as an array [<name>, [<version>, <quantity>]{1..x}]{0..x}
     * @static
     * @see BenutzerStatistik_Helper::parseUserAgents
     */
    public static function parseUserAgentsForSummary($user_agents) {
        $result = array();
        foreach ($user_agents as $user_agent) {
            $quantity = $user_agent['quantity'];
            $agent = $user_agent['user_agent'];
            $version = '?';
            if (preg_match('~(\d+(?:\.\d+)*)\s+(Safari)~i', $agent, $matches)) {
                $agent = $matches[2];
                $version = $matches[1];
            } elseif (preg_match('~(Epiphany|Camino|Safari|SeaMonkey|IceWeasel|Konqueror|Netscape|Opera|Firefox|MSIE|Galeon)[ /]([^;\s]+)(?:;|\s|$)~iS', $agent, $matches)) {
                $agent = $matches[1];
                $version = $matches[2];
            } elseif (preg_match('/(?:GranParadiso|Shiretoko|Namoroka|BonEcho|Minefield)[ \/]([^;\s]+)(?:;|\s|$)/iS', $agent, $matches)) {
                $agent = 'Firefox';
                $version = $matches[1];
            } else {
                $version = $agent;
                $agent = _('unbekannt');
            }

            $hash = md5($agent);
            if (!empty($result[$hash])) {
                if (empty($result[$hash]['versions'][$version])) {
                    $result[$hash]['versions'][$version] = array(
                        'user_agent'=>$user_agent['user_agent'],
                        'quantity'=>0
                    );
                }
                $result[$hash]['versions'][$version]['quantity'] += $quantity;
            } else {
                $result[$hash] = array(
                    'name'=>$agent,
                    'versions'=>array(
                        $version=>array(
                            'user_agent'=>$user_agent['user_agent'],
                            'quantity'=>$quantity
                        )
                    )
                );
            }
        }

        return $result;
    }

    public function getStatistics($date = null, $end_date = null) {
        $query_append = '';
        if (!is_null($date) and is_null($end_date)) {
            $query_append .= " daystamp = DATE(FROM_UNIXTIME(".$date."))";
        } elseif (!is_null($date) and !is_null($end_date)) {
            $query_append .= " daystamp BETWEEN DATE(FROM_UNIXTIME(".$date.")) AND DATE(FROM_UNIXTIME(".$end_date."))";
        }

        $query = "SELECT COUNT(DISTINCT hash) AS headcount, DATE_FORMAT(MIN(daystamp), '%d.%m.%Y') AS start_date, DATE_FORMAT(MAX(daystamp), '%d.%m.%Y') AS end_date, COUNT(*) AS visits, SUM(hits) AS hits, SUM(javascript) AS javascript, SUM(internal) AS internal, SUM(ajax) AS ajax, SUM(mobile) AS mobile, SUM(tablet) AS tablet FROM user_statistics";
        if (!empty($query_append)) {
            $query .= " WHERE".$query_append;
        }

        $row = DBManager::Get()->query($query)->fetch(PDO::FETCH_ASSOC);
        $stats = array(
            'date' => (!is_null($date) and is_null($end_date))
                ? date('d.m.Y', $date)
                : array(
                    'start' => $row['start_date'],
                    'end'   => $row['end_date'],
                ),
            'visits'       => $row['visits'],
            'headcount'    => $row['headcount'],
            'hits'         => $row['hits'],
            'javascript'   => $row['javascript'],
            'internal'     => $row['internal'],
            'ajax'         => $row['ajax'],
            'mobile'       => $row['mobile'],
            'tablet'       => $row['tablet'],
            'screen_sizes' => array(),
            'user_agents'  => array(),
            'permissions'  => array(),
        );

        if (is_null($date) or !is_null($end_date)) {
            $temp = DBManager::Get()->query("SELECT DATE_FORMAT(MIN(daystamp), '%d.%m.%Y') AS start_date FROM user_statistics_daily".(empty($query_append)?'':' WHERE'.$query_append))->fetchColumn();
            if ($temp) {
                $stats['date']['start'] = $temp;
            }
        }

        $tracked = array_map('reset', DBManager::get()->query("SELECT `url_id`, `description` FROM `user_statistics_tracked_urls_config` WHERE `active` = '1' ORDER BY `url_id` ASC")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN));

        $count_statement      = DBManager::get()->prepare("SELECT SUM(`clicks`) FROM `user_statistics_tracked_urls` WHERE `url_id` = ?".(empty($query_append)?'':' AND '.$query_append));
        $head_count_statement = DBManager::get()->prepare("SELECT COUNT(DISTINCT `user_hash`) FROM `user_statistics_tracked_urls` WHERE `url_id` = ?".(empty($query_append)?'':' AND '.$query_append));

        $stats['tracked'] = array();
        foreach ($tracked as $id => $title) {
            $count_statement->execute(array($id));
            $head_count_statement->execute(array($id));

            $stats['tracked'][$title] = array(
                'count'      => $count_statement->fetchColumn(),
                'head_count' => $head_count_statement->fetchColumn(),
            );
        }

        $query = "SELECT COUNT(*) AS quantity, COUNT(DISTINCT hash) AS headcount, permission FROM user_statistics";
        if (!empty($query_append)) {
            $query .= " WHERE".$query_append;
        }
        $query .= " GROUP BY permission";

        $rows = DBManager::Get()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $stats['permissions'][ $row['permission']] = $row['quantity'];
            $stats['permissions'][ $row['permission'].'_headcount'] = $row['headcount'];
        }

        $rows = DBManager::Get()->query("SELECT COUNT(*) AS quantity, screen_width, screen_height FROM user_statistics WHERE screen_width IS NOT NULL AND screen_height IS NOT NULL ".(empty($query_append)?'':" AND".$query_append)."GROUP BY screen_width, screen_height")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $stats['screen_sizes'][ $row['screen_width'].'x'.$row['screen_height'] ] = array(
                'width'    => $row['screen_width'],
                'height'   => $row['screen_height'],
                'quantity' => $row['quantity'],
            );
        }

        if (is_null($date) and is_null($end_date)) {
            $rows = DBManager::Get()->query("SELECT width, height, quantity FROM user_statistics_screensizes WHERE 0 NOT IN (width, height)")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (!isset($stats['screen_sizes'][ $row['width'].'x'.$row['height'] ]))
                    $stats['screen_sizes'][ $row['width'].'x'.$row['height'] ] = array(
                        'width'=>$row['width'],
                        'height'=>$row['height'],
                        'quantity'=>0,
                    );
                $stats['screen_sizes'][ $row['width'].'x'.$row['height'] ]['quantity'] += $row['quantity'];
            }
        }
        usort($stats['screen_sizes'], create_function('$a,$b', 'return $b["quantity"]-$a["quantity"];'));
        $stats['screen_sizes'] = array_slice($stats['screen_sizes'], 0, 10);

        $query = "SELECT user_agent, COUNT(*) AS quantity FROM user_statistics ".(empty($query_append)?'':" WHERE".$query_append)."GROUP BY user_agent";
        $user_agents = DBManager::Get()->query($query)->fetchAll(PDO::FETCH_ASSOC);

        if (is_null($date) and is_null($end_date)) {
            $stats['headcount'] = 0;

            $rows = DBManager::Get()->query("SELECT SUM(visits) AS visits, COUNT(*) unique_visits, SUM(hits) AS hits, permission FROM user_statistics_uniqueusers GROUP BY permission")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $stats['headcount'] += $row['unique_visits'];
                $stats['visits']    += $row['visits'];
                $stats['hits']      += $row['hits'];

                $stats['permissions'][ $row['permission'] ] += $row['visits'];
                $stats['permissions'][ $row['permission'].'_headcount'] = $row['unique_visits'];
            }

            $stats['javascript'] += DBManager::Get()->query("SELECT 0+SUM(javascript) FROM user_statistics_monthly")->fetchColumn();

            $result = array();
            $rows = DBManager::Get()->query("SELECT os, quantity FROM user_statistics_os")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row)
                $result[ $row['os'] ] = array(
                    'name'=>$row['os'],
                    'quantity'=>$row['quantity'],
                );

            $stats['os'] = BenutzerStatistik_Helper::parseOperatingSystems($user_agents, $result);

            $result = array();
            $rows = DBManager::Get()->query("SELECT agent_hash, browser, version, user_agent, quantity FROM user_statistics_browser")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row)
            {
                $quantity = $row['quantity'];

                $agent = _('unbekannt');
                $version = $row['user_agent'];

                if (!is_null($row['browser']) or !is_null($row['version']))
                {
                    $agent = $row['browser'];
                    $version = $row['version'];
                }
                $agent_hash = md5($agent);

                if (!isset($result[$agent_hash]))
                    $result[$agent_hash] = array(
                        'name'=>$agent,
                        'versions'=>array(),
                        'quantity'=>0
                    );

                if (!isset($result[$agent_hash]['versions'][$version]))
                    $result[$agent_hash]['versions'][$version] = 0;

                $result[$agent_hash]['quantity'] += $quantity;
                $result[$agent_hash]['versions'][$version] += $quantity;
            }

            $stats['user_agents'] = BenutzerStatistik_Helper::parseUserAgents($user_agents, $result);

            // TODO This should be one query
            $stats['ajax'] += DBManager::get()->query("SELECT SUM(ajax) FROM user_statistics_monthly")->fetchColumn();
            $stats['mobile'] += DBManager::get()->query("SELECT SUM(mobile) FROM user_statistics_monthly")->fetchColumn();
            $stats['tablet'] += DBManager::get()->query("SELECT SUM(tablet) FROM user_statistics_monthly")->fetchColumn();
            $stats['internal'] += DBManager::Get()->query("SELECT SUM(internal) FROM user_statistics_monthly")->fetchColumn();
        } else {
            $stats['os'] = BenutzerStatistik_Helper::parseOperatingSystems($user_agents);
            $stats['user_agents'] = BenutzerStatistik_Helper::parseUserAgents($user_agents);
        }

        $stats['user_agent_total'] = array_sum(array_pluck($stats['user_agents'], 'quantity'));
        $stats['screensize_total'] = array_sum(array_pluck($stats['screen_sizes'], 'quantity'));
        $stats['os_total'] = array_sum(array_pluck($stats['os'], 'quantity'));

        return $stats;
    }

    public function getMonthNames() {
        return array(
             1 => _('Januar'),
             2 => _('Februar'),
             3 => _('März'),
             4 => _('April'),
             5 => _('Mai'),
             6 => _('Juni'),
             7 => _('Juli'),
             8 => _('August'),
             9 => _('September'),
            10 => _('Oktober'),
            11 => _('November'),
            12 => _('Dezember'),
        );
    }
}
