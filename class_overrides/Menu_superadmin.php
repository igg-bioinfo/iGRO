<?php

class Menu_superadmin extends Menu {
    protected function set_menu_tools() {
        URL::changeable_vars_reset();
        $this->add_menu_main(self::MENU_TOOLS, Language::find('tools'));
        foreach (self::SA_MENU as $saMenu) {
            $this->add_menu(URL::create_url($saMenu[0], $this->oArea->url, true), Language::find($saMenu[1]), self::MENU_TOOLS);
        }
        URL::changeable_vars_from_onload_vars();
    }
}
