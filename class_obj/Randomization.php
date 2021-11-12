<?php

class Randomization {

    public $oVisit = NULL;
    public $id_visita = NULL;
    public $id_paz = NULL;
    public $id = 0;
    public $arm = NULL;
    public $arm_text = '-';
    public $author = NULL;
    public $ludati = NULL;
    public $is_randomized = false;
    protected $extra_fields = [];

    //-----------------------------------------------------OVVERIDABLE METHODS-----------------------------------------------------
    function init() {}

    function get_arm_text() {
        $this->arm_text = $this->arm;
    }

    function set_extra_values() {}

    function get_text() {
        global $oVisit;
        if (isset($oVisit) && $oVisit->id == $this->oVisit->id) {
            return 'The subject has been randomized in Arm ' . $this->arm_text . '. ';
        } else {
            return 'The subject is in Arm ' . $this->arm_text . '. ';
        }
    }

    //-----------------------------------------------------STATIC-----------------------------------------------------
    static function get_all() {
        $oRands = [];
        $sql = "SELECT * FROM visit_randomization ";
        $params = [];
        $rows = Database::read($sql, $params);
        $class_rand = 'Randomization_' . Config::RAND_CLASS;
        foreach ($rows as $row) {
            $oRand = new $class_rand(NULL, $row);
            $oRand->set_by_row($row);
            $oRands[] = $oRand;
        }
        return $oRands;
    }

    static function get_by_paz($id_paz) {
        $sql = "SELECT * FROM visit_randomization WHERE id_paz = ? ";
        $params = [$id_paz];
        $rows = Database::read($sql, $params);
        $class_rand = 'Randomization_' . Config::RAND_CLASS;
        $oRand = NEW Randomization(NULL);
        if(count($rows)) {
            $oRand = new $class_rand(NULL, $rows[0]);
        }
        return $oRand;
    }

    //-----------------------------------------------------PUBLIC-----------------------------------------------------
    function __construct($oVisit, $row = []) {
        $this->init();
        if (isset($oVisit)) {
            $this->oVisit = $oVisit;
        } else if (count($row) > 0) {
            $this->set_by_row($row);
        }
    }
    
    function get() {
        if (!isset($this->oVisit)) {
            $this->oVisit = new Visit();
            $this->oVisit->get_by_id($this->id_visita);
        }
        $this->get_by_visit();
    }

    function update() {
        if (!$this->is_randomized()) {
            $this->assign();
        }
        $this->set_extra_values();
        $oExtraFields = new stdClass();
        foreach ($this->extra_fields as $extra_field) {
            $oExtraFields->{$extra_field} = (string) $this->{$extra_field};
        }
        $sql = "UPDATE visit_randomization 
            SET extra_fields = '" . json_encode($oExtraFields) . "'
            WHERE id_visita = ? ";
        $params = [$this->oVisit->id];
        Database::edit($sql, $params);
    }

    function is_randomized() {
        return $this->id != 0;
    }

    //-----------------------------------------------------PROTECTED-----------------------------------------------------
    protected function add_extra_field($name) {
        $this->extra_fields[] = $name;
    }

    protected function get_by_visit() {
        $sql = "SELECT * FROM visit_randomization WHERE id_visita = ? ";
        $params = [$this->oVisit->id];
        $rows = Database::read($sql, $params);
        if (count($rows) > 0) {
            $this->set_by_row($rows[0]);
        }
    }

    protected function assign() {
        global $oUser;
        $where = '';
        foreach ($this->extra_fields as $extra_field) {
            $value = (string) $this->{$extra_field};
            if ($value != '') {
                $where .= " AND extra_fields LIKE '%\"".$extra_field."\":\"".$value."\"%' ";
            }
        }
        $sql = "UPDATE visit_randomization 
            SET id_visita = ?, id_paz = ?, author = ?, ludati = NOW()
            WHERE id_random = (
                SELECT IFNULL(MIN(id_random), 0) FROM visit_randomization WHERE id_visita IS NULL
                ".$where."
            )
            ";
        $params = [$this->oVisit->id, $this->oVisit->id_paz, $oUser->id];
        Database::edit($sql, $params);
    }

    //-----------------------------------------------------PRIVATE-----------------------------------------------------
    private function set_by_row($row) {
        $this->id = $row['id_random'];
        $this->id_visita = $row['id_visita'];
        $this->id_paz = $row['id_paz'];
        $this->arm = $row['arm'];
        $this->author = $row['author'];
        $this->ludati = $row['ludati'];
        $extra_obj = json_decode($row['extra_fields']);
        foreach ($this->extra_fields as $extra_field) {
            $this->{$extra_field} = isset($extra_obj->{$extra_field}) ? (is_numeric($extra_obj->{$extra_field}) ? (int) $extra_obj->{$extra_field} : $extra_obj->{$extra_field}) : NULL;
        }
        $this->get_arm_text();
    }

}