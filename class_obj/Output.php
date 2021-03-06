<?php

class Output {
    //OVERRIDABLE VARIABLES
    public $need_check = false;

    //VARIABLES
    public $id = 0;
    public $oVisit = NULL;
    public $oScores = [];
    public $oRandomization = NULL;
    public $result = '';
    public $recipients = '';
    public $author = NULL;
    public $ludati = NULL;
    public $has_output = false;

    const POST_PREFIX = 'output_post_';

    protected $extra_fields = [];
    protected $extra_field_inputs = [];
    const EOS_NO = [0 => 1, 1 => 'no'];
    const EOS_CR = [0 => 2, 1 => 'Clinical remission'];
    const EOS_FAILURE = [0 => 3, 1 => 'treatment_failure'];
    const EOS_DROPPED = [0 => 4, 1 => 'dropped_out'];
    const FAIL_AE = [0 => 5, 1 => 'adverse_events'];
    const FAIL_PHYSICIAN = [0 => 6, 1 => 'Physician decision'];
    const FAIL_PATIENT = [0 => 7, 1 => 'Patient decision'];
    const FAIL_BAD_RESPONSE = [0 => 8, 1 => 'Insufficient clinical response'];
    const FAIL_ERROR_NOAE = [0 => 9, 1 => 'Medication error without AE'];
    const FAIL_NEW_ENROLL = [0 => 10, 1 => 'other_trials'];
    const FAIL_MAJOR_CHANGE = [0 => 11, 1 => 'Major change in treatment'];

    const VALUE_NA = [0 => 99, 1 => 'not_applicable'];
    public static $eos_array = [];

    private static function translate($array) {
        return [$array[0], Language::find($array[1])];
    }
    public static function set_eos_array() {
        Language::add_area('patient');
        Language::add_area('patient_criteria');
        self::$eos_array[] = self::translate(self::EOS_NO);
        self::$eos_array[] = self::translate(self::EOS_CR);
        self::$eos_array[] = self::translate(self::EOS_FAILURE);
        self::$eos_array[] = self::translate(self::EOS_DROPPED);

        self::$eos_array[] = self::translate(self::FAIL_MAJOR_CHANGE);
        self::$eos_array[] = self::translate(self::FAIL_AE);
        self::$eos_array[] = self::translate(self::FAIL_PHYSICIAN);
        self::$eos_array[] = self::translate(self::FAIL_PATIENT);
        self::$eos_array[] = self::translate(self::FAIL_BAD_RESPONSE);
        self::$eos_array[] = self::translate(self::FAIL_ERROR_NOAE);
        self::$eos_array[] = self::translate(self::FAIL_NEW_ENROLL);

    }

    public static function get_fail_text($id) {
        $text = '';
        foreach(self::$eos_array as $fail) {
            if ($id.'' == $fail[0].'') {
                $text = $fail[1];
                break;
            }
        }
        return $text == '' ? Language::find(self::VALUE_NA[1]) : $text;
    }

//-----------------------------------------------------OVERRIDABLE METHODS-----------------------------------------------------
    public function calculate($get_all_text = true) {
        $this->result = $this->get_oScores_text();
    }

    public function init() {

    }

    public function after_construct() {

    }

    public function draw_extra_field_inputs() {
        $html = '';
        foreach ($this->extra_field_inputs as $field => $label) {
            $id = self::POST_PREFIX . $field;
            $is_view = false;
            $value = $this->{$field};
            $html .= Form_input::createSelect($id, $label, self::${$field . '_array'}, $value, 2, $is_view, "check_select('" . $id . "');");
        }
        if ($html . '' != '') {
            $html = '<div class="row col-xs-12">' . $html . '</div>';
        }
        return $html;
    }

//-----------------------------------------------------PUBLIC-----------------------------------------------------
    public function __construct($oVisit = NULL) {
        self::set_eos_array();
        $this->init();
        foreach ($this->extra_fields as $extra_field) {
            $this->{$extra_field} = NULL;
        }
        $this->oVisit = $oVisit;
        if (isset($this->oVisit)) {
            $this->get_oScores();
            $this->get_oRandomization();
            $this->get_by_ids();
        }
        $this->after_construct();
    }

    public function save($is_post = false) {
        if ($is_post) {
            foreach ($this->extra_field_inputs as $extra_field_input => $extra_field_label) {
                $this->{$extra_field_input} = Security::sanitize(INPUT_POST, self::POST_PREFIX . $extra_field_input);
            }
        }
        if ($this->id == 0) {
            $this->create();
        } else {
            $this->update();
        }
    }

//-----------------------------------------------------PRIVATE-----------------------------------------------------
    private function get_by_ids() {
        $sql = "SELECT * FROM visit_output WHERE id_visita = ? ";
        $params = [$this->oVisit->id];
        $rows = Database::read($sql, $params);
        if (count($rows) > 0) {
            $this->set_by_row($rows[0]);
        }
    }

    private function set_by_row($row) {
        if (count($row) > 0) {
            $this->id = $row['id_output'];
            $this->result = $row['result'];
            $this->recipients = $row['recipients'];
            $this->author = $row['author'];
            $this->ludati = $row['ludati'];
            $extra_obj = json_decode($row['extra_fields']);
            foreach ($this->extra_fields as $extra_field) {
                $this->{$extra_field} = isset($extra_obj->{$extra_field}) ? (is_numeric($extra_obj->{$extra_field}) ? (int) $extra_obj->{$extra_field} : $extra_obj->{$extra_field}) : NULL;
            }
        }
    }

    private function create() {
        global $oUser;
        $oExtraFields = new stdClass();
        foreach ($this->extra_fields as $extra_field) {
            $oExtraFields->{$extra_field} = (string) $this->{$extra_field};
        }
        $sql = "INSERT INTO visit_output (id_visita, result, recipients, extra_fields, author, ludati) VALUES (?, ?, ?, '" . json_encode($oExtraFields) . "', ?, NOW()) ";
        $params = [$this->oVisit->id, $this->result, $this->recipients, $oUser->id];
        Database::edit($sql, $params);
    }

    private function update() {
        global $oUser;
        $sql_extra_fields = new stdClass();
        foreach ($this->extra_fields as $extra_field) {
            $sql_extra_fields->{$extra_field} = $this->{$extra_field};
        }
        $sql = "UPDATE visit_output SET extra_fields = '" . json_encode($sql_extra_fields) . "', result = ?, recipients = ?, author = ?, ludati = NOW() 
            WHERE id_visita = ? ";
        $params = [$this->result, $this->recipients, $oUser->id, $this->oVisit->id];
        Database::edit($sql, $params);
    }

    private function get_oScores() {
        //$this->oScores = Abstract_score::get_all_by_visit_enroll($this->oVisit);
    }

    //-----------------------------------------------------PROTECTED-----------------------------------------------------
    protected function get_oRandomization() {
        $class_rand = 'Randomization_' . $this->oVisit->type;
        if (class_exists($class_rand)) {
            $this->oRandomization = new $class_rand($this->oVisit);
            $this->oRandomization->get();
            $this->oRandomization->update();
        }
    }
    protected function get_label($value, $type) {
        foreach (self::${$type . '_array'} as $element) {
            if ($element[0] . '' == $value . '') {
                return $element[1];
            }
        }
    }

    protected function set_subtitle($title) {
        return '<b style="color:#CC0000;">' . Language::find($title) . ':</b>' . HTML::BR;
    }

    protected function get_oScore_text($oScore) {
        return isset($oScore) ? '<b>' . $oScore->get_whole_name() . '</b> = ' . $oScore->get_whole_result() . HTML::BR : '';
    }

    protected function get_oScores_text() {
        $html = '';
        $html .= $this->set_subtitle('Scores');
        foreach ($this->oScores as $oScore) {
            $html .= $this->get_oScore_text($oScore);
        }
        if (count($this->oScores) == 0) {
            $html .= 'No score has been evaluated at this visit for ' . HTML::BR;
        } else {
            $this->has_output = true;
        }
        return $html;
    }

    protected function add_extra_field($name, $label_input = '') {
        $this->extra_fields[] = $name;
        if ($label_input . '' != '') {
            $this->extra_field_inputs[$name] = $label_input;
        }
    }

}