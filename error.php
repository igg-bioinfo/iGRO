<?php

//--------------------------------INCLUDE
include_once('class/Config.php');
include_once('class/Globals.php');
include_once('class_obj/User.php');


//--------------------------------VARIABLES
$html = '';
$area_id = Security::sanitize(INPUT_GET, 'area');
Security::sanitize(INPUT_GET, 'err');
Security::sanitize(INPUT_GET, 'aes');
Language::set(isset($_SESSION[URL::$prefix . "iso"]) ? $_SESSION[URL::$prefix . "iso"] : Config::LANGUAGEISO);


//--------------------------------AREA
Area::get_all();
$oAreaInvestigator = Area::get_by_property(Area::PROP_ID, Area::$ID_INVESTIGATOR);
$oArea = Area::get_by_property(Area::PROP_URL, $area_id);
if (!isset($oArea)) {
    $oArea = $oAreaInvestigator;
}

//--------------------------------URL SET
URL::set_vars($oArea->url);
URL::changeable_vars_reset();


//--------------------------------USER SESSION
session_start();
$ip_address = Security::sanitize(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
$oUser = isset($_SESSION[URL::$prefix . 'user']) ? $_SESSION[URL::$prefix . 'user'] : new User($ip_address);


//--------------------------------SET BUTTON BACK
$page_back = $oUser->is_logged() ? $oArea->default_page : 'login';
$button_back = '';
$sql = "SELECT * FROM page_template WHERE id_area = ? AND page_url LIKE ? ";
$params = [$oArea->id, '%' . $page_back];
$page_exist = Database::read($sql, $params);

//----------LOGGED
if ($oUser->is_logged()) {
    if (count($page_exist) == 0 || !$oUser->check_access($oArea)) {
        $oArea = $oUser->oAccessedArea;
    }
    $button_back = HTML::set_button(Language::find($oArea->default_page), '', URL::create_url($page_back, $oArea->url));
}

//----------NO LOGGED
else {
    $button_back = HTML::set_button(Language::find('login'), '', URL::create_url($page_back, $oArea->url));
}
$html .= HTML::BR . '<center>' . $button_back . '</center>';


//--------------------------------CSS & JS
HTML::set_default_css_js();


//--------------------------------HTML
$oMenu = new Menu();
$error_id = Security::sanitize(INPUT_GET, 'err') . '' != '' ? Security::sanitize(INPUT_GET, 'err') : URL::get_error();
Message::write($error_id);
HTML::print_html($html);
