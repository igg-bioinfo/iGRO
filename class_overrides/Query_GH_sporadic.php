<?php

class Query_Gh_sporadic extends Query {
    public $is_blocking = false;

    protected function init() {
        $this->sql = " SELECT IFNULL(continuity,0) AS continuity FROM (
            SELECT SUM(IFNULL(continuity, 0)) AS continuity FROM (
                SELECT 1 AS continuity FROM drug_therapy WHERE therapy_continuity >= 2 AND id_visita = ?
                UNION ALL
                SELECT 1 AS continuity FROM ae_occurrences WHERE therapy_discontinuation = 1 AND id_visita = ?
            ) AS F
        ) AS F ";
        $this->params = [$this->id_visita, $this->id_visita];
    }
    protected function set_by_row($row) {
        $this->has_issue = $row['continuity'] != 0;
        if ($this->has_issue) {
            if (Language::$iso == 'it') {
                $this->description = 'Lo terapia con GH risulta discontinua';
                $this->action = 'Controlla i dati inseriti nelle schede sia della terapia che degli eventi avversi';
            } else {
                $this->description = 'The GH therapy has been discontinous according to the visit data';
                $this->action = 'Check the data entered in therapy form and adverse event form';
            }
        }
    }
}