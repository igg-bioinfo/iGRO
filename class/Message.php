<?php

class Message {

    const TYPE_ERROR = 1;
    const TYPE_WARNING = 2;
    const TYPE_OK = 3;

    private static $messages = [
        //GENERAL
        ['id' => 1, 'message' => 'access_denied'],
        ['id' => 2, 'message' => 'no_page'],
        ['id' => 3, 'message' => 'post'],
        ['id' => 666, 'message' => 'token_error'],
        ['id' => 777, 'message' => 'query'],
        ['id' => 4, 'message' => 'no_username'],
        ['id' => 5, 'message' => 'no_center_pw'],
        ['id' => 6, 'message' => 'no_criteria'],
        ['id' => 7, 'message' => 'file_exists'],
        ['id' => 8, 'message' => 'no_privilege'],
        //VISIT
        ['id' => 101, 'message' => 'date_disc_error'],
        ['id' => 102, 'message' => 'subject_used'],
        ['id' => 103, 'message' => 'date_start_error'],
        ['id' => 105, 'message' => 'physician_confirm'],
        ['id' => 106, 'message' => 'all_confirmed'],
        //INSTRUCTION
        ['id' => 6664, 'message' => 'ins_census_modify'],
        ['id' => 6665, 'message' => 'ins_census_locked'],
        ['id' => 6666, 'message' => 'ins_criteria_modify'],
        ['id' => 6667, 'message' => 'ins_criteria_locked'],
        ['id' => 6668, 'message' => 'census_male_turner'],
        //CRF DEBUG
        ['id' => 7777, 'message' => 'bad_validation'],
        ['id' => 7778, 'message' => 'bad_page_n'],
        ['id' => 7779, 'message' => 'bad_field_n'],
        ['id' => 7780, 'message' => 'bad_field_n_page'],
        ['id' => 7781, 'message' => 'double_field'],
        ['id' => 7782, 'message' => 'no_field'],
        ['id' => 7783, 'message' => 'no_main_field'],
        ['id' => 7784, 'message' => 'too_records'],
        //URL ERROR
        ['id' => 403, 'message' => 'access_denied'],
        ['id' => 404, 'message' => 'no_resource'],
        ['id' => 500, 'message' => 'server_error'],
        //OK MESSAGE CHECK START FOR 555
        ['id' => 55501, 'message' => 'unlock_requested'],
    ];

    public static function get($id) {
        for ($m = 0; $m < count(self::$messages); $m++) {
            if (self::$messages[$m]['id'] . '' == $id . '') {
                return self::$messages[$m];
            }
        }
        return ['id' => 0, 'message' => ''];
    }

    public static function write($id = 0, $type = self::TYPE_ERROR) {
        $error_number = $id == 0 ? URL::get_error() : $id;
        if ($error_number.'' != '') {
            $error = self::get($error_number);
            $text = Language::find($error['message'], ['error']);
            if (Strings::startsWith($error_number, '555')) { $type = self::TYPE_OK; }
            switch($type) {
                case self::TYPE_ERROR:
                    HTML::$js_onload .= ' $("#table_error").show(); $("#table_text_error").html("'.$text.'"); ';
                    break;
                case self::TYPE_WARNING:
                    HTML::$js_onload .= ' $("#table_warning").show(); $("#table_text_warning").html("'.$text.'"); ';
                    break;
                case self::TYPE_OK:
                    HTML::$js_onload .= ' $("#table_ok").show(); $("#table_text_ok").html("'.$text.'"); ';
                    break;
            }
        }
    }

}
