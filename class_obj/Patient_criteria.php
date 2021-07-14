<?php

class Patient_criteria {

    public $id_paz = 0;
    public $oInclusion = NULL;
    public $oExclusion = NULL;
    public $oExtra = NULL;
    public $oAuthor = NULL;
    public $ludati = '';

    const INCLUSION = 1;
    const EXCLUSION = 2;
    const EXTRA = 3;
    

    //-----------------------------------------------------STATIC
    static function has_paz($id_paz) {
        $check = Database::read("SELECT DISTINCT id_paz FROM patient_criteria WHERE id_paz = ?", [$id_paz]);
        return count($check) > 0;
    }
    

    //-----------------------------------------------------CONSTRUCT
    function __construct($id_paz) {
        $this->id_paz = $id_paz;
        $this->inizialize();
    }

    private function inizialize() {
        $this->oAuthor = new User();
        $this->oInclusion = new Form('criteria_inc', $this->id_paz);
        $this->oExclusion = new Form('criteria_exc', $this->id_paz);
        $this->oExtra = new Form('criteria_extra', $this->id_paz);
        $this->set_types();
        $this->get_values();
        $this->get_author_ludati();
    }

    function set_types() {
        $this->oInclusion->add_key_field('patient_criteria', 'criteria_type', self::INCLUSION);
        $this->oExclusion->add_key_field('patient_criteria', 'criteria_type', self::EXCLUSION);
        $this->oExtra->add_key_field('patient_criteria', 'criteria_type', self::EXTRA);
    }


    public function get_values() {
        if ($this->id_paz != 0) {
            $this->oInclusion->get_values();
            $this->oExclusion->get_values();
            $this->oExtra->get_values();
        }
    }

    public function save() {
        if ($this->id_paz != 0) {
            $this->oInclusion->save();
            $this->oExclusion->save();
            $this->oExtra->save();
            $this->inizialize();
        }
    }

    function delete() {
        if ($this->id_paz != 0) {
            $this->oInclusion->delete();
            $this->oExclusion->delete();
            $this->oExtra->delete();
        }
    }
    

    //-----------------------------------------------------PRIVATE
    private function get_author_ludati() {
        $author_ludati = Database::read("SELECT author, ludati FROM patient_criteria WHERE id_paz = ? order by ludati DESC", [$this->id_paz]);
        if (count($author_ludati) > 0) {
            $this->oAuthor->get_by_id($author_ludati[0]['author']);
            $this->ludati = $author_ludati[0]['ludati'];
        }
    }
}