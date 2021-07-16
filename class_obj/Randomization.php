<?php

class Randomization {

    public $oVisit = NULL;
    public $id_visita = NULL;
    public $id = 0;
    public $arm = NULL;
    public $arm_text = NULL;
    public $author = NULL;
    public $ludati = NULL;
    public $is_randomized = false;
    protected $extra_fields = [];

    //-----------------------------------------------------OVVERIDABLE METHODS-----------------------------------------------------
    function init() {}

    function set_extra_values() {}

    function get_text() {
        global $oVisit;
        if (isset($oVisit) && $oVisit->id == $this->oVisit->id) {
            return 'The subject has been randomized in Arm ' . $this->arm . '. ';
        } else {
            return 'The subject is in Arm ' . $this->arm . '. ';
        }
    }

    //-----------------------------------------------------STATIC-----------------------------------------------------
    static function get_all($oVisit) {
        $oRands = [];
        $sql = "SELECT * FROM visit_randomization";
        $params = [$oVisit->id];
        $rows = Database::read($sql, $params);
        $class_rand = 'Randomization_' . $oVisit->type;
        foreach ($rows as $row) {
            $oRand = new $class_rand(NULL, $row);
            $oRand->set_by_row($row);
            $oRands[] = $oRand;
        }
        return $oRands;
    }

    protected function add_extra_field($name) {
        $this->extra_fields[] = $name;
    }

    //-----------------------------------------------------PUBLIC-----------------------------------------------------
    function __construct($oVisit, $row = []) {
        $this->init();
        if (isset($oVisit)) {
            $this->oVisit = $oVisit;
        } else 
        if (count($row) > 0) {
            $this->set_by_row($row);
        }
    }
    
    function get($by_visit) {
        if (!isset($this->oVisit)) {
            $this->oVisit = new Visit();
            $this->oVisit->get_by_id($this->id_visita);
        }
        if ($by_visit) {
            $this->get_by_visit();
        } else {
            $this->get_by_paz();
        }
    }
    protected function get_by_paz() {
        $sql = "SELECT * FROM visit_randomization WHERE id_paz = ? ";
        $params = [$this->oVisit->id_paz];
        $rows = Database::read($sql, $params);
        if (count($rows) > 0) {
            $this->set_by_row($rows[0]);
        }
    }

    protected function get_by_visit() {
        $sql = "SELECT * FROM visit_randomization WHERE id_visita = ? ";
        $params = [$this->oVisit->id];
        $rows = Database::read($sql, $params);
        if (count($rows) > 0) {
            $this->set_by_row($rows[0]);
        }
    }

    function is_randomized() {
        return $this->id != 0;
    }

    //-----------------------------------------------------PROTECTED-----------------------------------------------------
    protected function assign() {
        global $oUser;
        $sql = "UPDATE visit_randomization 
                SET id_visita = ?, id_paz = ?, author = ?, extra_fields = '{}', ludati = NOW()
                WHERE id_random = (SELECT IFNULL(MIN(id_random), 0) FROM visit_randomization WHERE id_visita IS NULL)
                ";
        $params = [$this->oVisit->id, $this->oVisit->id_paz, $oUser->id];
        Database::edit($sql, $params);
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

    //-----------------------------------------------------PRIVATE-----------------------------------------------------
    private function set_by_row($row) {
        $this->id = $row['id_random'];
        $this->id_visita = $row['id_visita'];
        $this->arm = $row['arm'];
        $this->author = $row['author'];
        $this->ludati = $row['ludati'];
        $extra_obj = json_decode($row['extra_fields']);
        foreach ($this->extra_fields as $extra_field) {
            $this->{$extra_field} = isset($extra_obj->{$extra_field}) ? (int) $extra_obj->{$extra_field} : NULL;
        }
    }

}