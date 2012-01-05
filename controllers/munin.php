<?php
class MuninController extends StudipController
{
    /**
     * Common code for all actions: set default layout and page title.
     */
    public function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();

        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base_without_infobox');
        $this->set_layout($layout);

        PageLayout::setTitle('Benutzerstatistik - Administration');
        Navigation::activateItem('/benutzerstatistik/munin');

    }

    public function index_action()
    {

    }
}