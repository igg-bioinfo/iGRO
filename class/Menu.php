<?php

class Menu {

    const MENU_USER = 'user_menu';
    const MENU_TOOLS = 'tools_menu';
    const MENU_PATIENT = 'pt_menu';
    const MENU_VISIT = 'visit_menu';

    const SA_MENU = [
        ["centers", 'centers'],
        ["users", 'users'],
        //["diagnosis", 'diagnosis'],
        ["visit_types", 'visits'],
        ["forms", 'forms'],
        ["translations", 'translations']
    ];

    protected $oUser = NULL;
    protected $oArea = NULL;
    protected $oProject = NULL;
    protected $oPatient = NULL;
    protected $oVisit = NULL;
    protected $page_name = NULL;
    protected $menu_links = array();

    public function __construct() {
        global $oUser, $oArea, $oPatient, $oVisit, $page_url;
        $this->oUser = $oUser;
        $this->oArea = $oArea;
        $this->oPatient = $oPatient;
        $this->oVisit = $oVisit;
        $this->page_name = $page_url;
        if (!isset($this->page_name) || $this->page_name . '' == '') {
            return;
        }
        if ($this->page_name != 'login' && $oUser->is_logged()) {
            if (in_array($this->oArea->id, [Area::$ID_ADMIN, Area::$ID_INVESTIGATOR, Area::$ID_SUPERADMIN])) {
                $this->set_menu_tools();
                if ($this->oPatient->id != 0) {
                    $this->set_menu_patient();
                    if ($this->oVisit->id != 0) {
                        $this->set_menu_visit();
                    }
                }
            }
            $this->set_menu_user();
        }
        URL::changeable_vars_from_onload_vars();
    }

    public function draw() {
        global $class_menu;
        echo '<div id="navbar" class="navbar-collapse collapse" >';
        echo '<ul class="navbar-nav mr-auto">';
        $link_sel = false;
        foreach ($this->menu_links as $key => $menu) {
            if ($key == '') {
                echo $this->draw_menu_link($link_sel, $menu[0], $menu[1], true);
            } else {
                echo $this->draw_menu_main($key, $menu[0], $menu[1]);
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    public function has() {
        return count($this->menu_links) > 0;
    }

    //----------------------------------------------MENU FUNCTIONS----------------------------------------------
    protected function set_menu_user() {
        $this->add_menu_main(self::MENU_USER, $this->oUser->name);
        $this->add_menu(URL::create_url("logout", "", true), Language::find('logout'), self::MENU_USER);
    }

    protected function set_menu_tools() {
        URL::changeable_vars_reset();
        $this->add_menu_main(self::MENU_TOOLS, Language::find('tools'));
        if ($this->oArea->id == Area::$ID_ADMIN) {
            $this->add_menu(URL::create_url("removed", $this->oArea->url, true), 'Subjects / visits removed', self::MENU_TOOLS);
        } else if (in_array($this->oArea->id, [Area::$ID_INVESTIGATOR])) {
            $this->add_menu(URL::create_url("exporting_data", $this->oArea->url, true), 'Data export', self::MENU_TOOLS);
        }

        URL::changeable_vars_from_onload_vars();
    }

    protected function set_menu_patient() {
        URL::changeable_vars_reset_except(['pid']);
        $this->add_menu_main(self::MENU_PATIENT, $this->oPatient->patient_id);
        $this->add_menu(URL::create_url("patient_index", $this->oArea->url, true), Language::find('patient_index'), self::MENU_PATIENT);
        if (in_array($this->oArea->id, [Area::$ID_INVESTIGATOR])) {
            $this->add_menu(URL::create_url("patient_census", $this->oArea->url, true), Language::find('patient_census'), self::MENU_PATIENT);
        }
        $this->add_menu(URL::create_url("patient_criteria", $this->oArea->url, true), Language::find('patient_criteria'), self::MENU_PATIENT);
        //$this->add_menu(URL::create_url("patient_status", $this->oArea->url, true), Language::find('end_form'), self::MENU_PATIENT);
        //$this->add_menu(URL::create_url("patient_graph", $this->oArea->url, true), 'Graphs', self::MENU_PATIENT);
        if ($this->oArea->id == Area::$ID_ADMIN) {
            //$this->add_menu(URL::create_url("scores", $this->oArea->url, true), 'Scores', self::MENU_PATIENT);
        }
        $this->add_menu(URL::create_url("visits", $this->oArea->url, true), Language::find('visits'), self::MENU_PATIENT);
        $oVisits = Visit::get_all_by_id_paz($this->oPatient->id);
        foreach ($oVisits as $oVis) {
            URL::changeable_var_add('vid', $oVis->id);
            $this->add_menu(URL::create_url("visit_index", $this->oArea->url, true), Date::default_to_screen($oVis->date) . ' - ' . $oVis->type_text, self::MENU_PATIENT);
        }
        URL::changeable_vars_from_onload_vars();
    }

    protected function set_menu_visit() {
        URL::changeable_vars_reset_except(['pid', 'vid']);
        $this->add_menu_main(self::MENU_VISIT, $this->oVisit->type_text);
        $this->add_menu(URL::create_url("visit_index", $this->oArea->url, true), Language::find('forms'), self::MENU_VISIT);
        $oMenuFrms = Form::get_all_by_visit_type($this->oVisit->type_id, $this->oPatient->id, $this->oVisit->id);
        if ($this->oVisit->has_output) {
            $this->add_menu(URL::create_url("output", $this->oArea->url, true), Language::find('output'), self::MENU_VISIT);
        }
        foreach ($oMenuFrms as $oFrmMenu) {
            URL::changeable_var_add('fid', $oFrmMenu->id);
            URL::changeable_var_add('act', 'view');
            $this->add_menu(URL::create_url("form", $this->oArea->url, true), $oFrmMenu->get_title(), self::MENU_VISIT);

        }
        URL::changeable_vars_from_onload_vars();
    }

    //----------------------------------------------MANAGE FUNCTIONS----------------------------------------------
    protected function add_menu($url, $page_text, $menu_group = NULL) {
        if (isset($this->menu_links[$menu_group])) {
            $this->menu_links[$menu_group][1][] = [$url, $page_text];
        } else {
            $this->menu_links[''] = [$url, $page_text];
        }
    }

    protected function add_menu_main($menu_group, $menu_name) {
        $this->menu_links[$menu_group] = [$menu_name, array()];
    }

    //------------------------------DRAW FUNCTIONS

    protected function draw_menu_main($menu_group, $menu_name, $links) {
        $main = '';
        $links_text = '';
        foreach ($links as $link) {
            $links_text .= $this->draw_menu_link($link[0], $link[1], false);
        }
        $main .= '<li id="menu_' . $menu_group . '" class="nav-item dropdown">';
        $main .= '<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" ';
        $main .= 'role="button" aria-expanded="false" onclick="mobile_click(\'' . $menu_group . '\');">';
        $main .= $menu_name . '<span class="caret"></span>';
        $main .= '</a>';
        $main .= '<ul class="dropdown-menu" role="menu">';
        $main .= $links_text;
        $main .= '</ul>';
        $main .= '</li>';
        return $main;
    }

    protected function draw_menu_link($page_url, $page_text, $is_direct = false) {
        $link_text = '';
        $url = Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . str_replace(' ', '_', $page_url);
        $link_text .= '<li class="dropdown-item"';
        $link_text .= ' style="cursor: pointer; width: auto; padding-top: 8px;' . ($is_direct ? '' : '') . '" onclick="javascript:window.location.href=\'' . $url . '\';">';
        $link_text .= $page_text;
        $link_text .= '</li>';
        return $link_text;
    }
}
