<?php
class TrackerController extends StudipController {

    public function sniff_action($width, $height) {
        DBManager::Get()
            ->prepare("UPDATE `user_statistics` SET `javascript` = 1, `screen_width` = ?, `screen_height` = ? WHERE `ip` = MD5(INET_ATON(?)) AND `user_agent` = ? AND TRIM(`hash`) = ? AND `daystamp` = CURDATE()")
            ->execute(array(
                (int)$width,
                (int)$height,
                $_SERVER['REMOTE_ADDR'],
                empty($_SERVER['HTTP_USER_AGENT']) ? '?' : $_SERVER['HTTP_USER_AGENT'],
                md5($GLOBALS['auth']->auth['uid'].$GLOBALS['auth']->auth['uname']),
            ));

        $_SESSION['BENUTZERSTATISTIK']['js'] = true;

        $this->render_nothing();
    }

    public function url_action($id) {
        $id = (int)$id;

        // Get URL
        $statement = DBManager::Get()->prepare("SELECT url FROM user_statistics_tracked_urls_config WHERE url_id = ?");
        $statement->execute(array($id));
        $this->url = $statement->fetch(PDO::FETCH_COLUMN);

/*
        // Redirect
        if (!headers_sent()) {
            Header('Location: '.$url);
        } else {
            echo '<script type="text/javascript">location.href="'.$url.'";</script><noscript><meta http-equiv="Refresh" content="0;URL='.$url.'" /></noscript>';
        }

        // Prevent output
        $this->render_nothing();
*/
        // Store hit
        DBManager::get()
            ->prepare("INSERT DELAYED INTO `user_statistics_tracked_urls` (`url_id`, `user_hash`, `daystamp`, `clicks`) VALUES (?, MD5(?), NOW(), 1) ON DUPLICATE KEY UPDATE `clicks` = `clicks` + 1")
            ->execute(array($id, $GLOBALS['auth']->auth['uid']));
    }
}
