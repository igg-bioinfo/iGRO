<?php

class Menu_admin extends Menu {
    protected function set_menu_tools() {
        URL::changeable_vars_reset();
        $this->add_menu_main(self::MENU_TOOLS, Language::find('tools'));
        $this->add_menu(URL::create_url("users", $this->oArea->url, true), Language::find('users'), self::MENU_TOOLS);
        URL::changeable_vars_from_onload_vars();
    }
}
