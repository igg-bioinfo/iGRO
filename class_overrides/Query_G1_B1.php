<?php

class Query_G1_B1 extends Query {
    public $is_blocking = false;

    protected function init() {
        $this->sql = "SELECT tanner_stage_gb FROM data_at_visit WHERE id_visita = ?;";
        $this->params = [$this->id_visita];
    }
    protected function set_by_row($row) {
        $this->has_issue = $row['tanner_stage_gb'] != 1;
        if ($this->has_issue) {
            global $oPatient;
            $is_male = $oPatient->gender == '1';
            if (Language::$iso == 'it') {
                $this->description = 'Lo stadio di Tanner dovrebbe essere '.($is_male ? 'G' : 'B').'1';
                $this->action = 'Il paziente non soddisfa più i criteri di inclusione e uscirà dallo studio ';
            } else {
                $this->description = 'The Tanner stage shoud be '.($is_male ? 'G' : 'B').'1';
                $this->action = 'Check the data entered in subject data form';
            }
        }
    }
}