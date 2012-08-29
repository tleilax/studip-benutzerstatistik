<?php
class GraphsController extends StudipController
{
    /**
     * Common code for all actions: set default layout and page title.
     */
    public function before_filter(&$action, &$args) {
        $this->plugin = $this->dispatcher->plugin;

        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox');
        $this->set_layout($layout);


        PageLayout::setTitle('Benutzerstatistik - Graphen');
        if ($action == 'tracked')
            Navigation::activateItem('/benutzerstatistik/graphs/tracked-'.Request::int('id', reset($args)));
        else
            Navigation::activateItem('/benutzerstatistik/graphs/'.$action);


        $this->image_path = $this->plugin->getPluginURL().'/assets/images/';
        $this->show_hits = Config::GetInstance()->getValue('BENUTZERSTATISTIK_STORE_HITS');
    }

    public function index_action()
    {
        $month_names = BenutzerStatistik_Helper::getMonthNames();

        if (empty($_GET['month']))
        {
            $month = date('n');
            $year = date('Y');
        }
        else
            list($year, $month) = explode('-', $_GET['month']);

        $months = array();
        $rows = DBManager::Get()->query("SELECT DISTINCT month_stamp FROM (SELECT DISTINCT DATE_FORMAT(daystamp, '%Y-%c') AS month_stamp FROM user_statistics UNION SELECT DISTINCT DATE_FORMAT(daystamp, '%Y-%c') AS month_stamp FROM user_statistics_daily) AS tmp_table ORDER BY month_stamp DESC")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $month_stamp)
        {
            list($y, $m) = explode('-', $month_stamp);
            $months[$month_stamp] = $month_names[$m].' '.$y;
        }

        $stats = array();
        $totals = array(
            'hits'=>0,
            'visits'=>0
        );
        $rows = DBManager::Get()->query("SELECT DAY(daystamp) AS day, permission, SUM(hits) AS hits, COUNT(hash) AS visits, COUNT(DISTINCT hash) AS headcount FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME(".mktime(0,0,0,$month,1,$year).") AND FROM_UNIXTIME(".(mktime(0,0,0,$month+1,1,$year)-1).") GROUP BY daystamp, permission UNION SELECT DAY(daystamp) AS day, permission, SUM(hits) AS hits, SUM(visits) AS visits, SUM(unique_visits) AS headcount FROM user_statistics_daily WHERE daystamp BETWEEN FROM_UNIXTIME(".mktime(0,0,0,$month,1,$year).") AND FROM_UNIXTIME(".(mktime(0,0,0,$month+1,1,$year)-1).") GROUP BY daystamp, permission")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row)
        {
            if (empty($stats[ $row['day'] ]))
                $stats[ $row['day'] ] = array('total'=>array(
                    'hits'      => 0,
                    'visits'    => 0,
                    'headcount' => 0,
                ));
            $stats[ $row['day'] ][ $row['permission'] ] = array(
                'hits'      => $row['hits'],
                'visits'    => $row['visits'],
                'headcount' => $row['headcount'],
            );
            $stats[ $row['day'] ]['total']['hits'] += $row['hits'];
            $stats[ $row['day'] ]['total']['visits'] += $row['visits'];
            $stats[ $row['day'] ]['total']['headcount'] += $row['headcount'];
            $totals['hits'] += $row['hits'];
            $totals['visits'] += $row['visits'];
        }

        $max = array();
        $average = array(
            'headcount'=>0,
        );
        $average_count = 0;
        foreach ($stats as $stat) {
            foreach ($stat as $type => $data) {
                foreach ($data as $index => $value) {
                    if (empty($max[$index]))
                        $max[$index] = 0;
                    $max[$index] = max($max[$index], $value);

                    if ($type === 'total' && $index === 'headcount')  {
                        $average[$index] += $value;
                    }
                }
            }
        }

        // TODO: Hier anpassen
        $totals['headcount'] = DBManager::Get()->query("SELECT MAX(headcount)+0 FROM (SELECT COUNT(DISTINCT hash) AS headcount FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME(".mktime(0,0,0,$month,1,$year).") AND FROM_UNIXTIME(".(mktime(0,0,0,$month+1,1,$year)-1).") UNION SELECT unique_visits AS headcount FROM user_statistics_monthly WHERE month_stamp=LAST_DAY(FROM_UNIXTIME(".mktime(0,0,0,$month,1,$year)."))) AS tmp_table")->fetchColumn();

        $this->type         = $_REQUEST['type'] ?: 'visits';
        $this->months       = $months;
        $this->month_stamp  = mktime(0, 0, 0, $month, 1, $year);
        $this->stats        = $stats;
        $this->max          = $max;
        $this->totals       = $totals;
        $this->average      = $average;
        $this->visits_scale = $this->getScale($max['visits']);
        $this->hits_scale   = $this->getScale($max['hits']);
        $this->max_days     = $year . $month === date('Ym')
                            ? date('j')
                            : date('t', mktime(0, 0, 0, $month, 1, $year));
        
    }

    public function quarterly_action()
    {
        $quarters = array();

        $rows = DBManager::Get()->query("SELECT DISTINCT quarter_stamp FROM (SELECT DISTINCT CONCAT(YEAR(daystamp),'-',CEIL(MONTH(daystamp)/3)) AS quarter_stamp FROM user_statistics UNION SELECT DISTINCT CONCAT(YEAR(daystamp),'-',CEIL(MONTH(daystamp)/3)) AS quarter_stamp FROM user_statistics_daily) AS tmp_table ORDER BY quarter_stamp DESC")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $quarter_stamp)
        {
            list($year, $quarter) = explode('-', $quarter_stamp);
            $quarters[$quarter_stamp] = array(
                'quarter' => $quarter,
                'year'    => $year,
            );
        }

        if (empty($_GET['quarter']))
        {
            $quarter = ceil(date('m')/3);
            $year = date('Y');
        }
        else
            list($year, $quarter) = explode('-', $_GET['quarter']);
        $quarter_stamp = mktime(0,0,0, $quarter*3-2, 1, $year);

        $stats = array();
        $rows = DBManager::Get()->query("SELECT day, hits, visits, headcount FROM (SELECT DATE_FORMAT(daystamp, '%c-%e') AS day, SUM(hits) AS hits, COUNT(hash) AS visits, COUNT(DISTINCT hash) AS headcount FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME({$quarter_stamp}) AND FROM_UNIXTIME(".(strtotime('3 months', $quarter_stamp)-1).") GROUP BY daystamp UNION SELECT DATE_FORMAT(daystamp, '%c-%e') AS day, SUM(hits) AS hits, SUM(visits) AS visits, SUM(unique_visits) AS headcount FROM user_statistics_daily WHERE daystamp BETWEEN FROM_UNIXTIME({$quarter_stamp}) AND FROM_UNIXTIME(".(strtotime('3 months', $quarter_stamp)-1).") GROUP BY daystamp) AS tmp_table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row)
            $stats[ $row['day'] ] = array(
                'hits'      => $row['hits'],
                'visits'    => $row['visits'],
                'headcount' => $row['headcount'],
            );

        $max = array();
        $totals = array();
        $average = array(
            'visits'    => 0,
            'headcount' => 0,
            'hits'      => 0,
        );
        $average_count = 0;
        foreach ($stats as $stat)
        {
            foreach ($stat as $index=>$value)
            {
                if (empty($totals[$index]))
                    $totals[$index] = 0;
                $totals[$index] += $value;

                if (empty($max[$index]))
                    $max[$index] = 0;
                $max[$index] = max($max[$index], $value);

                $average[$index] += $value;
            }
            $average_count++;
        }
        foreach ($average as $index=>$value)
            $average[$index] = $value/$average_count;

        $totals['headcount'] = DBManager::Get()->query("SELECT COUNT(DISTINCT hash) FROM (SELECT hash FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME({$quarter_stamp}) AND FROM_UNIXTIME(".(strtotime('3 months', $quarter_stamp)-1).") UNION SELECT hash FROM user_statistics_uniqueusers_monthly WHERE month BETWEEN ".(ceil(date('m', $quarter_stamp)/3)*3)." AND ".(ceil(date('m', $quarter_stamp)/3)*3+2)." AND year=".date('Y', $quarter_stamp).") AS tmp_table")->fetchColumn();

        $days = date('t', $quarter_stamp)+date('t', strtotime('1 month', $quarter_stamp))+date('t', strtotime('2 months', $quarter_stamp));

        $day_captions = array();
        $current_caption = array(
            'title'=>'1',
            'span'=>1
        );
        for ($i=1, $stamp=strtotime('tomorrow', $quarter_stamp); $i<$days; $i++, $stamp=strtotime('tomorrow', $stamp))
        {
            $day = date('j', $stamp);
            if ($day%5==0 or $day==1)
            {
                array_push($day_captions, $current_caption);
                $current_caption = array(
                    'title'=>date('j', $stamp),
                    'span'=>0
                );
            }
            $current_caption['span']+=1;
        }
        if ($current_caption['span'] != 0)
            array_push($day_captions, $current_caption);

        $this->type = empty($_REQUEST['type']) ? 'visits' : $_REQUEST['type'];
        $this->links = array(
            'submit'  => PluginEngine::getLink('benutzerstatistik/graphs/quarterly'),
            'monthly' => PluginEngine::getLink('benutzerstatistik/graphs/index'),
        );
        $this->quarters = $quarters;
        $this->quarter_stamp = $quarter_stamp;
        $this->days = $days;
        $this->td_width = round(95/$days, 2);
        $this->stats = $stats;
        $this->totals = $totals;
        $this->average = $average;
        $this->max = $max;
        $this->day_captions = $day_captions;
        $this->visits_scale = $this->getScale($max['visits']);
        $this->hits_scale = $this->getScale($max['hits']);
        $this->months = BenutzerStatistik_Helper::getMonthNames();
    }

    public function uni_yearly_action()
    {
        $years = DBManager::Get()->query("SELECT DISTINCT year FROM (SELECT DISTINCT YEAR(TIMESTAMPADD(MONTH, -9, daystamp)) AS year FROM user_statistics UNION SELECT DISTINCT YEAR(TIMESTAMPADD(MONTH, -9, month_stamp)) AS year FROM user_statistics_monthly) AS tmp_table ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);

        $year = empty($_GET['year']) ? date('Y', strtotime('-9 months')) : $_GET['year'];

        $start_date = mktime(0,0,0,10,1,$year);
        $end_date = mktime(23,59,59,9,30,$year+1);
        $this->getAggregatedStatistics($start_date, $end_date);

        $this->years = $years;
        $this->year = $year;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->links = array(
            'monthly' => PluginEngine::getLink('benutzerstatistik/graphs/index'),
        );
        $this->monthnames = BenutzerStatistik_Helper::getMonthNames();
    }

    public function yearly_action()
    {
        $years = DBManager::Get()->query("SELECT DISTINCT year FROM (SELECT DISTINCT YEAR(daystamp) AS year FROM user_statistics UNION SELECT DISTINCT YEAR(month_stamp) AS year FROM user_statistics_monthly) AS tmp_table ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);

        $year = empty($_GET['year']) ? date('Y') : $_GET['year'];

        $start_date = mktime(0, 0, 0, 1, 1, $year);
        $end_date = mktime(23, 59, 59, 12, 31, $year);
        $this->getAggregatedStatistics($start_date, $end_date);

        $this->start_date = $start_date;
        $this->end_date = $end_date;

        $this->years = $years;
        $this->year = $year;
        $this->links = array(
            'monthly' => PluginEngine::getLink('benutzerstatistik/graphs/index'),
        );
        $this->monthnames = BenutzerStatistik_Helper::getMonthNames();
    }

    public function tracked_action($id) {
        $statement = DBManager::get()->prepare("SELECT DISTINCT YEAR(`daystamp`) FROM `user_statistics_tracked_urls` WHERE `url_id` = ?");
        $statement->execute(array($id));
        $years = $statement->fetchAll(PDO::FETCH_COLUMN);

        $year = Request::int('year', date('Y'));

        $start_date = mktime(00, 00, 00, 01, 01, $year);
        $end_date   = mktime(23, 59, 59, 12, 31, $year);

        $total = $max = $average = 0;
        $total_headcount = $max_headcount = $average_headcount = 0;
        $months = $head_count = array();

        $statement = DBManager::get()->prepare("SELECT MONTH(`daystamp`) AS `month`, 0 + SUM(`clicks`) AS `clicks`, 0 + COUNT(DISTINCT `user_hash`) AS `head_count` FROM `user_statistics_tracked_urls` WHERE `daystamp` BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?) AND `url_id` = ? GROUP BY MONTH(`daystamp`)");
        $statement->execute(array(
            $start_date,
            $end_date,
            $id,
        ));
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $months[ $row['month'] ] = $row['clicks'];
            $max = max($max, $row['clicks']);
            $average += $row['clicks'];
            $total += $row['clicks'];

            $head_count[ $row['month'] ] = $row['head_count'];
            $max_headcount = max($max_headcount, $row['head_count']);
            $average_headcount += $row['head_count'];
        }

        $statement = DBManager::get()->prepare("SELECT COUNT(DISTINCT `user_hash`) FROM user_statistics_tracked_urls WHERE daystamp BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?) AND url_id = ?");
        $statement->execute(array($start_date, $end_date, $id));
        $total_headcount = $statement->fetchColumn();

        $average = count($months)
                 ? $average / count($months)
                 : 0;
        $average_headcount = count($head_count)
                           ? $average_headcount / count($head_count)
                           : 0;

        $this->id = $id;
        $this->months = $months;
        $this->total = $total;
        $this->max = $max;
        $this->average = $average;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->years = $years;
        $this->year = $year;
        $this->head_count = $head_count;
        $this->max_headcount = $max_headcount;
        $this->total_headcount = $total_headcount;
        $this->average_headcount = $average_headcount;
        $this->monthnames = BenutzerStatistik_Helper::getMonthNames();

        $statement = DBManager::get()->prepare("SELECT `description` FROM `user_statistics_tracked_urls_config` WHERE `url_id` = ?");
        $statement->execute(array($id));
        $this->title = $statement->fetch(PDO::FETCH_COLUMN);
    }

    private function getScale($max)
    {
        $possible_scales = array(5, 10, 25, 50, 100);
        $test_scales = $possible_scales;

        do
        {
            $test_scale = array_shift($test_scales);
            if (empty($test_scales))
            {
                foreach (array_keys($possible_scales) as $index)
                    $possible_scales[$index] *= 10;
                $test_scales = $possible_scales;
            }
        }
        while ($max / $test_scale > 10);

        $scale = array();
        $current_scale = 0;
        while ($current_scale < $max)
        {
            array_push($scale, array(
                'value'   => $current_scale,
                'percent' => $current_scale / $max,
            ));
            $current_scale += $test_scale;
        }

        return $scale;
    }

    private function getAggregatedStatistics($start_date, $end_date)
    {
        $totals = $max = $average = array(
            'visits'       => 0,
            'uniquevisits' => 0,
            'hits'         => 0,
        );

        $months = array();
        $rows = DBManager::Get()->query("SELECT month, permission, visits, uniquevisits, hits FROM (SELECT MONTH(daystamp) AS month, permission, COUNT(*) AS visits, COUNT(DISTINCT hash) AS uniquevisits, SUM(hits) AS hits, SUM(internal) AS internal FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME({$start_date}) AND FROM_UNIXTIME({$end_date}) GROUP BY MONTH(daystamp), permission UNION SELECT MONTH(month_stamp) AS month, permission, SUM(visits) AS visits, SUM(unique_visits) AS uniquevisits, SUM(hits) AS hits, SUM(internal) AS internal FROM user_statistics_monthly WHERE month_stamp BETWEEN FROM_UNIXTIME({$start_date}) AND FROM_UNIXTIME({$end_date}) GROUP BY MONTH(month_stamp), permission) AS tmp_table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row)
        {
            if (!isset($months[ $row['month'] ]))
                $months[ $row['month'] ] = array(
                    'total' => array(
                        'visits'        => 0,
                        'unique_visits' => 0,
                        'hits'          => 0,
                        'internal'      => 0,
                    ),
                );
            $months[ $row['month'] ][ $row['permission'] ] = array(
                'visits'       => $row['visits'],
                'uniquevisits' => $row['uniquevisits'],
                'hits'         => $row['hits'],
                'internal'     => $row['internal'],
            );

            foreach (array('visits', 'uniquevisits', 'hits', 'interval') as $key)
            {
                $max[$key]      = max($max[$key], $row[$key]);
                $average[$key] += $row[$key];
                $totals[$key]  += $row[$key];
                $months[ $row['month'] ]['total'][$key] += $row[$key];
            }
        }

        foreach (array_keys($average) as $index)
            $average[$index] /= count($months);

        $totals['uniquevisits'] = DBManager::Get()->query("SELECT COUNT(DISTINCT hash) FROM (SELECT DISTINCT hash FROM user_statistics WHERE daystamp BETWEEN FROM_UNIXTIME({$start_date}) AND FROM_UNIXTIME({$end_date}) UNION SELECT DISTINCT hash FROM user_statistics_uniqueusers_monthly WHERE DATE(CONCAT(year,'-',month,'-1')) BETWEEN FROM_UNIXTIME({$start_date}) AND FROM_UNIXTIME({$end_date})) AS tmp_table")->fetchColumn();

        $this->months = $months;
        $this->max = $max;
        $this->average = $average;
        $this->totals = $totals;
    }
}
