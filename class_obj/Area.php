<?php

abstract class Area {

    public $id = 0;
    public $name = '';
    public $url = '';
    public $color_font = NULL;
    public $color_background = NULL;
    public $default_page = 'patients';

    const PROP_ID = 'id';
    const PROP_NAME = 'name';
    const PROP_URL = 'url';
    const MAX_LOGIN_ATTEMPTS = 5;

    // LOGIN RESULT
    public $login_result = '';
    public $link_expiration = 3600;

    const LOGIN_SUCCESS = 'User is logged in.';
    const USER_BLOCKED = 'User has been blocked by the system.';
    const USER_NOT_EXIST = 'Username does not exist.';
    const WRONG_PASSWORD = 'Password is wrong.';
    const INVALID_PASSWORD_FORMAT = 'Password format is invalid.';
    const USER_NO_PERMISSION = 'User does not have permission for area.';

    public static $list = [];
    public static $records = [];
    public static $ID_ADMIN = 0;
    public static $ID_INVESTIGATOR = 0;
    public static $ID_SUPERADMIN = 0;

    //----------------------------------------STATIC
    public static function get_all() {
        $sql = "SELECT A.* FROM area A ORDER BY area_name";
        $params = [];
        self::$records = Database::read($sql, $params);
        self::$list = [];
        foreach (self::$records as $area) {
            $class = 'Area_' . $area['area_url'];
            if (class_exists($class)) {
                $oArea = new $class($area);
                self::$list[] = $oArea;
            }
        }
    }

    public static function get_by_property($property, $value) {
        foreach (self::$list as $oArea) {
            if ($oArea->{$property} == $value) {
                return $oArea;
            }
        }
        return NULL;
    }

    //----------------------------------------CONSTRUCT
    function __construct($row) {
        if (count($row) > 0) {
            $this->set_by_row($row);
        }
        $this->init_static_id();
    }

    //----------------------------------------ABSTRACT
    abstract function init_static_id();
    abstract function login(&$users, $username, $password);

    //----------------------------------------PUBLIC OVERRIDABLE

    // return wrong password, success, user not found, blocked
    function check_access_and_login($username, $password, $ip_address) {
        $this->login_result = '';
        $users = [];
        $oUser = false;

        // login fail manage
        $sql = "SELECT COUNT(id_fail) AS attempts, email  FROM session_fail
                WHERE email = ? AND ip_address = ? AND id_area = ? AND is_unlock = 0
                GROUP BY email";

        $accesses = Database::read($sql, [$username, $ip_address, $this->id]);
        if (!empty($accesses) && $accesses[0]["attempts"] >= self::MAX_LOGIN_ATTEMPTS) {
            $this->login_result = self::USER_BLOCKED;
        } else {
            $this->login_result = $this->login($users, $username, $password);
            if ($this->login_result == self::LOGIN_SUCCESS) {
                $this->unblock($username, $ip_address);
            }
        }

        // object user creation
        if ($this->login_result == self::LOGIN_SUCCESS) {
            $oUser = new User($ip_address, $users[0], $this);
            $this->check_password_duration($users[0]['pswdate'], $oUser);
        } else {
            $this->record_failed_login($this->login_result, $username, $password, $ip_address);
        }
        return $oUser;
    }

    function unblock($username, $ip_address) {
        $sql = "DELETE FROM session_fail WHERE email = ? AND ip_address = ? AND id_area = ?  AND is_unlock = 0";
        Database::edit($sql, [$username, $ip_address, $this->id]);
    }

    function record_failed_login($login_result, $username, $password, $ipaddress) {
        if (in_array($login_result, [self::USER_BLOCKED, self::USER_NO_PERMISSION, self::USER_NOT_EXIST])) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            Error_log::$code = 403;
            Error_log::$id_area = $this->id;
            Error_log::$description = json_encode(['username' => $username, 'password' => $password, 'ip' => $ipaddress]);
            Error_log::$message = $login_result;
            Error_log::set('LOGIN', false);
        }

        if (in_array($login_result, [self::USER_BLOCKED, self::WRONG_PASSWORD])) {
            $sql = "INSERT INTO session_fail (email, password_sha, ip_address, ludati, id_area, is_unlock) VALUES (?, ?, ?, NOW(), ?, 0)";
            $password = password_hash($password, PASSWORD_DEFAULT);
            Database::edit($sql, [$username, $password, $ipaddress, $this->id]);
        }

        if ($login_result === self::WRONG_PASSWORD) {
            $sql = "SELECT COUNT(id_fail) AS attempts, email  FROM session_fail
                WHERE email = ? AND ip_address = ? AND id_area = ? AND is_unlock = 0
                GROUP BY email";

            $accesses = Database::read($sql, [$username, $ipaddress, $this->id]);
            if (!empty($accesses) && $accesses[0]["attempts"] === self::MAX_LOGIN_ATTEMPTS) {
                $this->send_blocked_user_notification_email(User::get_by_email($username));
            }
        }
    }

    function change_password(User &$user) {
        URL::changeable_vars_reset();
        URL::changeable_var_add('mtp', 'forgot-pw-reset');
        URL::changeable_var_add('email', $user->email);
        URL::changeable_var_add('validity', (time() + 3600));
        $url = URL::create_url('login', $this->url);
        return $this->send_password_email($user, $url);
    }

    public function get_regex() {
        return '^(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\\' . join('\\', Globals::SPECIAL_CHARACTERS) . '])[0-9a-zA-Z\\' . join('\\', Globals::SPECIAL_CHARACTERS) . ']{10,}$';
    }

    function update_password(User $user, $new_password) {
        $old_password_hash = Database::read("SELECT * FROM " . User::get_table() . " t1 WHERE id_user = ?", [$user->id])[0][0];
        if (!$this->check_password_format($new_password, $user)) {
            return false;
        }
        if (password_verify($new_password, $old_password_hash)) {
            URL::changeable_var_add('mtp', 'reset-pw-fail-same');
            URL::redirect('login');
        }
        $sql = "UPDATE user SET password = ?, pswdate = NOW() WHERE id_user = ?";
        $params = [password_hash($new_password, PASSWORD_DEFAULT), $user->id];
        Database::edit($sql, $params);
        return true;
    }

    public function check_password_format($new_password, User $user) {
        if (!preg_match('/' . $this->get_regex() . '/', $new_password)) { // check format
            Error_log::$code = 500;
            Error_log::$id_area = $this->id;
            Error_log::$description = json_encode(['username' => $user->email, 'password' => $new_password, 'ip' => $user->ip_address]);
            Error_log::$message = self::INVALID_PASSWORD_FORMAT;
            Error_log::set('LOGIN', false);
            return false;
        }

        return true;
    }

    public function send_password_email(User &$user, $new_password) {
        $oMailer = new Mailer();
        $message = Language::find('new_pw_msg');
        $message = str_replace('%%%', $user->name . ' ' . $user->surname, $message);
        $message = str_replace('$$0$$', $new_password, $message);
        $oMailer->set_subject(Language::find('new_pw_obj'));
        $oMailer->set_message($message);
        return $oMailer->send([$user]);
    }

    //-----------------------------------------------------PRIVATE
    private function send_blocked_user_notification_email(User $user) {
        $oMailer = new Mailer();
        $message = Language::find('acc_suspended_msg');
        $message = str_replace('%%%', $user->name . ' ' . $user->surname, $message);
        $message = str_replace('$$0$$', $user->email, $message);
        $oMailer->set_subject(Language::find('acc_suspended_obj'));
        $oMailer->set_message($message);

        return $oMailer->send([$user, Mailer::get_admin()]);
    }

    private function check_password_duration($pswdate, User $user) {
        $pswdate = new DateTime($pswdate);
        $now = new DateTime();
        $pswduration = $pswdate->diff($now)->format("%a");
        if ($pswduration > Globals::MAX_PASSWORD_DURATION) {
            $this->change_password($user, '');
            URL::changeable_vars_reset();
            URL::changeable_var_add('mtp', 'pw-expired');
            URL::redirect('login');
        }
    }

    private function set_by_row($row) {
        $this->id = isset($row["id_area"]) ? $row["id_area"] : 0;
        $this->name = isset($row["area_name"]) ? $row["area_name"] : '';
        $this->url = isset($row["area_url"]) ? $row["area_url"] : '';
        $this->color_font = isset($row["color_font"]) ? $row["color_font"] : '';
        $this->color_background = isset($row["color_background"]) ? $row["color_background"] : '';
    }

}
