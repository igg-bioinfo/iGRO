<?php

//--------------------------------INCLUDE
include_once('class/Config.php');
include_once('class/Globals.php');
if (Config::UNDER_MAINTENANCE) {
    header("Location: " . Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . 'under_maintenance.html');
    exit;
}

include_once('class_obj/User.php');

function custom_error($error_code, $error_message, $error_file, $error_line) {
    if (isset($error_code)) {
        Error_log::$code = $error_code;
    }
    if (isset($error_message)) {
        Error_log::$message = $error_message;
    }
    if (isset($error_file)) {
        Error_log::$file = $error_file;
    }
    if (isset($error_line)) {
        Error_log::$line = $error_line;
    }
    Error_log::set('PHP');
}

set_error_handler("custom_error");


//--------------------------------VARIABLES
$area_id = Security::sanitize(INPUT_GET, 'area');
$ip_address = Security::sanitize(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
$page_url = Security::sanitize(INPUT_GET, 'pg');
Security::sanitize(INPUT_GET, 'aes');
Security::sanitize(INPUT_GET, 'at');
Security::sanitize(INPUT_GET, 'av');
Area::get_all();
$oAreaInvestigator = Area::get_by_property(Area::PROP_ID, Area::$ID_INVESTIGATOR);
$oArea = Area::get_by_property(Area::PROP_URL, $area_id);
// AREA CHECK
if (!isset($oArea)) {
    URL::redirect('error', 1, $oAreaInvestigator->url);
}
URL::set_vars($oArea->url);



//--------------------------------USER & CHECK ACCESS
session_start();
$oUser = isset($_SESSION[URL::$prefix . 'user']) ? clone($_SESSION[URL::$prefix . 'user']) : new User($ip_address);
$access_id = URL::get_onload_var("acid");
$access_date = URL::get_onload_var("acdt");

if ((!$oUser->is_logged() || $oArea->id == Area::$ID_ADMIN) && $access_id . '' != '' && $access_date . '' != '') {
    URL::changeable_var_remove("acid");
    URL::changeable_var_remove("acdt");
    $sql = "SELECT * FROM session WHERE id_area = ? AND id_user = ? AND ip_address = ?";
    $params = [$oArea->id, $access_id, $ip_address];
    $access_session = Database::read($sql, $params);
    $mins_spent = Date::date_difference(Date::default_to_object($access_date, true), new DateTime(), Date::INTERVAL_MINS);
//    Error_log::$code = 666;
//    Error_log::$id_area = $oArea->id;
//    Error_log::$description = 'QUERY: ' . (isset($access_session) && count($access_session) > 0 ? count($access_session) : 'no result') . '; ';
//    Error_log::$description .= 'IP: ' . $ip_address . '; ';
//    Error_log::$description .= 'MINS: ' . $mins_spent . '; ';
//    Error_log::$message = 'Access for ' . $access_id;
//    Error_log::set('ACCESS', false);
    if (count($access_session) > 0 && $mins_spent < Globals::LOGIN_LINK_EXPIRED_MIN) {
        $oUser->get_by_id($access_id);
        $oUser->oAccessedArea = $oArea;
        $_SESSION[URL::$prefix . 'user'] = $oUser;
        if ($oArea->id == Area::$ID_ADMIN) {
            URL::changeable_vars_reset();
            URL::redirect($oArea->default_page);
        }
    }
}
URL::changeable_var_remove("acid");
URL::changeable_var_remove("acdt");
if ($oUser->is_logged() && !$oUser->check_access($oArea)) {
    URL::redirect('error', 1, $oAreaInvestigator->url);
}


//--------------------------------LOGOUT
if ($page_url == 'logout') {
    $oUser->logout();
    URL::redirect("login", '', isset($oUser->oAccessedArea) ? $oUser->oAccessedArea->url : $oArea->url);
}


//--------------------------------PAGE FILE
Language::set(isset($_SESSION[URL::$prefix . "iso"]) ? $_SESSION[URL::$prefix . "iso"] : Config::LANGUAGEISO);
$sql = "SELECT DISTINCT PT.file_name, PT.need_login
    FROM page_template PT
    WHERE PT.id_area = ? AND PT.page_url = ?
    ORDER BY PT.page_url DESC ";
$file = Database::read($sql, array(&$oArea->id, &$page_url));

if (count($file) == 0) {
    URL::redirect('error', 2);
}


//--------------------------------CHECK TOKEN
if (!Security::check_post_token()) {
    if (Strings::startsWith($page_url, 'ajax')) {
        echo '{ "query": "Unit", "error": "access_denied" }';
        exit;
    } else {
        URL::redirect('error', 666);
    }
}


//--------------------------------CHECK USER LOGGED
if ($file[0]["need_login"]) {
    if ($oUser->is_logged()) {
        URL::changeable_var_add("fn", Globals::AJAX_SESSION);
        $ajax_url = URL::create_url("ajax");
        URL::changeable_var_remove("fn");
        $func_name = 'controlSessions';
        $js_always = 'setTimeout("' . $func_name . '();", 300000); '; //5 min
        HTML::$js .= JS::set_func($func_name, JS::set_ajax($ajax_url, 'json', '', '', '', $js_always));
        HTML::$js_onload .= ' ' . $func_name . '(); ';
    } else {
        $page_redirect = URL::get_onload_var('pg_rdr');
        if ($page_redirect != '') {
            URL::redirect('login');
        }
        URL::redirect('error', 1);
        URL::redirect('login');
    }
}

//--------------------------------CSS & JS
if (!Strings::startsWith($page_url, 'ajax')) {
    HTML::set_default_css_js();
    HTML::$logo_name = Config::TITLE;
}

//--------------------------------PATIENT
$oPatient = new Patient();
if (URL::get_onload_var('pid') != '') {
    $oPatient->get_by_id(URL::get_onload_var('pid'));
    if ($oArea->id == Area::$ID_INVESTIGATOR && isset($oUser->oCenter) && $oUser->oCenter->has_password()) {
        //USED TO DECRYPT WITH CENTER PASSWORD
        $oPatient->oCenter = $oUser->oCenter;
    }
}

//--------------------------------VISIT
$oVisit = new Visit();
if (URL::get_onload_var('vid') != '') {
    Language::add_area('visit');
    $oVisit->get_by_id(URL::get_onload_var('vid'));
}

//--------------------------------UPDATE SESSION & MENU
if (!Strings::startsWith($page_url, 'ajax')) {
    $class_menu = "Menu_" . $oArea->url;
    if (!class_exists($class_menu)) {
        $class_menu = "Menu_" . $oArea->url;
        if (!class_exists($class_menu)) {
            $class_menu = "Menu";
        }
    }
    $oMenu = new $class_menu();
    if ($oUser->is_logged()) {
        $session_logged = Database::read("SELECT id_user FROM session WHERE id_user = ? AND id_area = ? AND ip_address = ?; ",
            [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
        if (count($session_logged) == 0) {
            Database::read("INSERT INTO session (id_user, id_area, ip_address, date_login, last_seen) VALUES (?, ?, ?, NOW(), NOW()); ",
                [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
        } else {
            Database::read("UPDATE session SET last_seen = NOW() WHERE id_user = ? AND id_area = ? AND ip_address = ?; ",
                [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
        }
        //Database::redirect_error($oURL);
    }
}

//--------------------------------OPEN FILE
$html = '';
if (Config::SITEVERSION == 'TEST') {
    $no_debug_vars = defined('Config::NO_DEBUG_VARS');
    if (!$no_debug_vars) {
        HTML::$debug_info .= 'GLOBAL VARS ';
        foreach ($GLOBALS as $key => $global_var) {
            if (!Strings::startsWith($key, '_') && !in_array($key, ['GLOBALS', 'key', 'global_var', 'vars'])) {
                $vars[] = '$' . $key;
            }
        }
        HTML::$debug_info .= '(' . count($vars) . '): ' . join(', ', $vars)
                . HTML::set_br(2);
    }
}

if (strtolower($file[0]["file_name"]) == Globals::FORM_CLASS_FILE) {

    //--------------------------------OPEN CRF FORM
    if (URL::get_onload_var('fid') == '') {
        URL::redirect('error', 1);
    }
    $class_row = Database::read("SELECT form_class FROM form WHERE form_id = ? ", [URL::get_onload_var('fid')]);
    if (count($class_row) != 1) {
        URL::redirect('error', 1);
    }
    $class = ucfirst($class_row[0][0]);

    $class_path = Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'crf' . DIRECTORY_SEPARATOR . $class . '.php';
    if (!file_exists($class_path)) {
        URL::redirect('error', 1);
    }

    $path_sub = $path_crf = Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'crf';
    if (Strings::contains($class, DIRECTORY_SEPARATOR)) {
        $folder = substr($class, 0, strpos($class, DIRECTORY_SEPARATOR));
        $path_sub .= DIRECTORY_SEPARATOR . $folder;
        $class = str_replace($folder, "", $class);
    }

    function include_recursive($path) {
        if (file_exists($path)) {
            include_once $path;
            return true;
        }
        return false;
    }

    spl_autoload_register(function ($class) use($path_crf, $path_sub) {
        if (!include_recursive($path_crf . DIRECTORY_SEPARATOR . $class . '.php')) {
            include_recursive($path_sub . DIRECTORY_SEPARATOR . $class . '.php');
        }
    });

    $page = new $class();
    $page->render();
} else if (file_exists($file[0]["file_name"])) {
    include_once($file[0]["file_name"]);
} else {
    URL::redirect('error', 2);
}