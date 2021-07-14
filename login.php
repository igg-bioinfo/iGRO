<?php

//--------------------------------VARIABLES
$html = '';
$form = '';
$multi_page = URL::get_onload_var("mtp");

//--------------------------------CENTER-PW
$center_pw = Security::sanitize(INPUT_POST, 'center-pw');
if ($center_pw != '') {
    $oUser->oCenter->check_password($center_pw);
    if ($oUser->oCenter->has_password()) {
        $_SESSION[URL::$prefix . 'user'] = $oUser;
        URL::changeable_vars_reset();
        URL::redirect($oArea->default_page);
    } else {
        URL::redirect("", 5);
    }
}

//--------------------------------LOGIN
$username = Security::sanitize(INPUT_POST, 'username');
$pw_post = Security::sanitize(INPUT_POST, 'password');
$pw_quote = stripslashes($pw_post);
$password = $pw_quote;
if ($username != '' && $password != '' && isset($oArea)) {
    $oUser = $oArea->check_access_and_login($username, $password, $ip_address);
    if ($oUser) {
        $_SESSION[URL::$prefix . 'user'] = $oUser;
        URL::changeable_vars_reset();
        if ($oArea->id == Area::$ID_INVESTIGATOR) {
            URL::changeable_var_add('mtp', 'center-pw');
            URL::redirect("login");
        } else {
            $page_redirect = URL::get_onload_var('pg_rdr');
            if (empty($page_redirect)) {
                URL::changeable_vars_reset();
                URL::redirect($oArea->default_page);
            } else {
                $redirect = URL::create_url($page_redirect) . '/' . Security::sanitize(INPUT_GET, 'aes');
                header("Location: " . $redirect);
                exit;
            }
        }
    } else {
        URL::redirect("login", 1);
    }
}

//--------------------------------FORGOT PASSWORD
$email = Security::sanitize(INPUT_POST, 'new-pw');
if (!empty($email)) {
    $user = User::get_by_email($email);
    $message = '';
    if (is_null($user)) {
        Error_log::$code = 403;
        Error_log::$id_area = $oArea->id;
        Error_log::$description = json_encode(['ip' => $ip_address]);
        Error_log::$message = 'User ' . $email . ' does not exist in the system. Cannot reset password.';
        if ($message) {
            Error_log::$message = $message;
        }
        Error_log::set('LOGIN', false);

        URL::changeable_var_add('mtp', 'forgot-pw-success');
        URL::redirect('login');
    } else {
        if ($oArea->change_password($user, 8)) {
            URL::changeable_var_add('mtp', 'forgot-pw-success');
            URL::redirect("login");
        } else {
            URL::redirect("error", 500);
        }
    }
}

// Update password
$password = Security::sanitize(INPUT_POST, 'new-psw');
if (!empty($password)) {
    $email = Security::sanitize(INPUT_POST, 'email');
    $user = User::get_by_email($email, $ip_address);
    if (is_null($user)) {
        Error_log::$code = 500;
        Error_log::$id_area = $oArea->id;
        Error_log::$description = json_encode(['ip' => $ip_address]);
        Error_log::$message = 'User ' . $email . ' does not exist anymore in the system. Was it deleted after redirection?';
        Error_log::set('LOGIN', false);

        URL::redirect("error", 500);
    } else {
        if ($oArea->update_password($user, $password)) {
            URL::changeable_var_add('mtp', 'reset-pw-success');
            URL::redirect("login");
        } else {
            URL::redirect("error", 500);
        }
    }
}

//--------------------------------FORM
Language::add_area('auth');
$area_label = $oArea->name; //Language::find($oArea->name);
$login_label = Language::find('login');
$pw_label = Language::find('password');
$forgot_pw_label = Language::find('forgot_password');
$back_login_label = Language::find('back');

$form .= '<input type="text" style="display:none"  autocomplete="off" >'
        . '<input type="password" style="display:none" autocomplete="off">';

switch ($multi_page) {
    case 'center-pw':
        if (!$oUser->is_logged()) {
            URL::redirect("error", 1);
        }

        if ($oUser->oCenter->has_password()) {
            URL::changeable_vars_reset();
            URL::redirect($oArea->url);
        }

        HTML::$title = $oArea->name . ' - '. Language::find('center_pw');
        $form .= Form_input::createInputPassword('center-pw', Language::find('center_pw'), 6, true, "check_text('center-pw','','',8);", 8, 100)
                . HTML::set_button(Language::find('next'), "page_validation('form1');");
        if ($oArea->id == Area::$ID_ADMIN) {
            $form .= HTML::set_spaces(4);
            URL::changeable_vars_reset();
            $form .= HTML::set_button('Skip', '', URL::create_url("index"));
        }

        break;
    case 'forgot-pw':
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        $html .= '<h3 class="text-center">' . $forgot_pw_label . '</h3>';
        $form .= Form_input::createInputText('new-pw', Language::find('insert_mail'), '', 6, false, JS::call_func('check_mail', ['new-pw', true]), 100)
                . Form_input::br()
                . HTML::set_bootstrap_cell(HTML::set_button(Language::find('password_retrieve'), 'page_validation(\'form1\');'), 6, false, 'text-center')
                . Form_input::br()
                . HTML::set_bootstrap_cell('<a href="' . URL::create_url("login") . '">' . $back_login_label . '</a>', 6, false, 'text-center');

        break;

    case 'forgot-pw-success':
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        $form .= HTML::set_bootstrap_cell(HTML::set_text(Language::find('forgot_pw_confirm')))
                . Form_input::br(true)
                . HTML::set_bootstrap_cell(HTML::set_button($back_login_label, '', URL::create_url("login")), 6, false, 'text-center');
        break;

    case 'pw-expired':
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        $form .= HTML::set_bootstrap_cell(HTML::set_text(Language::find('pw_expired_confirm')), 6, false, 'text-center')
                . Form_input::br(true)
                . HTML::set_bootstrap_cell(HTML::set_button($back_login_label, '', URL::create_url("login")), 6, false, 'text-center');
        break;

    case 'reset-pw-fail-same':
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        $form .= HTML::set_bootstrap_cell(HTML::set_text(Language::find('pw_equal')), 6, false, 'text-center')
                . Form_input::br(true)
                . HTML::set_bootstrap_cell(HTML::set_button($back_login_label, '', URL::create_url("login")), 6, false, 'text-center');
        break;

    case 'forgot-pw-reset':
        $email = URL::get_onload_var("email");
        // get password expiration date
        // investigator
        if ($oArea->id == Area::$ID_INVESTIGATOR) {
            $res = Database::read('SELECT pswdate FROM user WHERE email = ?', [$email]);
            $psw_date = is_null($res[0][0]) ? 0 : $res[0][0]->getTimestamp();
        }
        $validity = URL::get_onload_var('validity');
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        if (is_null($validity) || time() > $validity || $psw_date > ($validity - $oArea->link_expiration)) {
            $form .= HTML::set_bootstrap_cell(HTML::set_text(Language::find('link_expired')), 6, false, 'text-center')
                . Form_input::br(true)
                . HTML::set_bootstrap_cell(HTML::set_button($back_login_label, '', URL::create_url("login")), 6, false, 'text-center');
            break;
        }

        $regex = addslashes($oArea->get_regex());

        $form .= Form_input::createInputPassword('new-psw', Language::find('new_password'), 6, false, JS::call_func('check_password', ['new-psw', $regex]), 100)
            . Form_input::br()
            . Form_input::createInputPassword('new-pw-confirm', Language::find('new_password2'), 6, false, JS::call_func('check_password_confirm', ['new-pw-confirm', 'new-psw']), 100)
            . Form_input::createHidden('email', $email)
            . Form_input::br()
            . HTML::set_bootstrap_cell(HTML::set_button(Language::find('reset_pw'), "page_validation('form1');"), 6, false, 'text-center')
            . Form_input::br()
            . HTML::set_bootstrap_cell('<a href="' . URL::create_url("login") . '">' . $back_login_label . '</a>', 6, false, 'text-center');

        break;

    case 'reset-pw-success':
        URL::changeable_vars_reset();
        if (isset($language_iso)) {
            URL::changeable_var_add('lng', $language_iso);
        }
        HTML::$title = $area_label;
        $form .= HTML::set_bootstrap_cell(HTML::set_text(Language::find('reset_pw_confirm')), 6, false, 'text-center')
            . Form_input::br(true)
            . HTML::set_bootstrap_cell(HTML::set_button($back_login_label, '', URL::create_url("login")), 6, false, 'text-center');
        break;

    default:
        if ($oUser->is_logged()) {
            $oUser->logout();
        }
        HTML::$title = $area_label . ' - ' . $login_label;
        $password_input = Form_input::createInputPassword('password', $pw_label, 6, true, JS::call_func('check_text', ['password']), 100);
        $form .= Form_input::createInputText('username', Language::find('username'), '', 6, true, 'if(check_text(\'username\',5)) {check_mail(\'username\'); }', 150)
            . $password_input
            . HTML::set_bootstrap_cell(HTML::set_button($login_label, 'page_validation(\'form1\');'), 6, false, 'text-center')
            . Form_input::br();

        URL::changeable_var_add('mtp', 'forgot-pw');
        $form .= HTML::set_bootstrap_cell('<a href="' . URL::create_url("login") . '">' . $forgot_pw_label . '</a>', 6, false, 'text-center');

        break;
}

$html .= HTML::set_form($form, 'form1', 'autocomplete="off"')
    . HTML::set_br()
    . HTML::set_row(HTML::set_bootstrap_cell(HTML::set_text(Language::find('browser_warning')
    . ':<br/><a href="https://www.google.com/chrome/">Chrome ≥ 58</a>
    <br/><a href="https://www.mozilla.org/it/firefox/new/">Firefox ≥ 54</a>
    <br/><a href="https://www.microsoft.com/it-it/download/Internet-Explorer-11-for-Windows-7-details.aspx">Internet Explorer 11</a>
    <br/>Safari ≥ 10', true), 6, false, 'alert alert-warning text-center', 'font-size:11px'), 'justify-content-center');
HTML::$js .= ' const SPECIALS = "' . join(' ', Globals::SPECIAL_CHARACTERS) . '";';
HTML::$js_onload .= '$(".row").addClass("justify-content-center");';
HTML::add_link("vendor/ua-parser.min", "js");
HTML::$js_onload .= 'browser_check();';

//--------------------------------------HTML
HTML::print_html($html);
