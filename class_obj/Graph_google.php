<?php

class Graph_google {

    //-------------------------------------------VARIABLES
    public $id = '';
    public $oCols = [];
    public $oOptions = [];
    private $rows = [];
    private $type = [];
    private $counter_values = [];

    const TYPE_TIMELINE = 'timeline';
    const COL_TYPE_STRING = 'string';
    const COL_TYPE_DATE = 'date';
    const COL_TYPE_NUMBER = 'number';

    //-------------------------------------------CONSTRUCT
    function __construct($id, $type) {
        $this->type = $type;
        HTML::$css .= ' div.google-visualization-tooltip {  } ';
        if ($this->type == self::TYPE_TIMELINE) {
            //HTML::add_link("https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1',packages:['timeline'],language:'en'}]}", 'extjs');
            //HTML::add_link("https://www.gstatic.com/charts/loader.js", 'extjs');
            HTML::add_link("https://www.google.com/jsapi", 'extjs');
        }
        $this->id = $id;
    }

    //-------------------------------------------PUBLIC
    function draw() {
        HTML::$js .= "google.charts.load('visualization', '1', {packages:['timeline'],language:'en'}); ";
        HTML::$js .= 'google.setOnLoadCallback(google_draw_' . $this->id . '); ';
        HTML::$js .= 'function google_draw_' . $this->id . '() { ';
        HTML::$js .= 'var container = document.getElementById("canvas_' . $this->id . '"); ';
        if ($this->type == self::TYPE_TIMELINE) {
            HTML::$js .= 'var chart = new google.visualization.Timeline(container); ';
        }
        HTML::$js .= 'var dataTable = new google.visualization.DataTable(); ';
        foreach ($this->oCols as $oCol) {
            HTML::$js .= 'dataTable.addColumn({ ';
            HTML::$js .= ' type: "' . $oCol->type . '", ';
            HTML::$js .= ' '.($oCol->id_key == '' ? 'id' : $oCol->id_key).': "' . $oCol->id . '" ';
            HTML::$js .= ' '.($oCol->id_key == 'role' ? ', "p": {"html": true}' : '').' ';
            HTML::$js .= ' }); ';
        }
        HTML::$js .= 'dataTable.addRows([ ';
        foreach ($this->rows as $row) {
            HTML::$js .= ' ' . $row . ' ';
        }
        HTML::$js .= ' ]); ';
        HTML::$js .= 'var options = { ';
        foreach ($this->oOptions as $oOpt) {
            HTML::$js .= ' ' . $oOpt->id . ': { ' . $oOpt->value . ' }, ';
        }
        HTML::$js .= '}; ';
        HTML::$js .= 'chart.draw(dataTable, options); ';
        HTML::$js .= '} ';
        return '<div id="canvas_' . $this->id . '" style="width: 100%; height: ' . ((2 + count($this->counter_values)) * 45) . 'px; "></div>';
    }

    function add_column($col_id, $col_type, $is_counter = false, $id_key = '') {
        if (!isset($this->oCols[$col_id])) {
            $oColumn = new stdClass();
            $oColumn->id = $col_id;
            $oColumn->type = $col_type;
            $oColumn->is_counter = $is_counter;
            $oColumn->id_key = $id_key;
            $oColumn->value = '';
            $this->oCols[$col_id] = $oColumn;
        }
    }

    function add_row() {
        $row = '[ ';
        foreach ($this->oCols as $oCol) {
            if ($oCol->is_counter && !in_array($oCol->value, $this->counter_values)) {
                $this->counter_values[] = $oCol->value;
            }
            switch ($oCol->type) {
                case self::COL_TYPE_STRING:
                    $row .= '"' . $oCol->value . '"';
                    break;

                case self::COL_TYPE_NUMBER:
                    $row .= '' . $oCol->value . '';
                    break;

                case self::COL_TYPE_DATE:
                    $row .= 'new Date(' . $oCol->value . ')';
                    break;
            }
            $row .= ', ';
        }
        $row .= ' ], ';
        $this->rows[] = $row;
    }

    function add_option($opt_id, $opt_value) {
        if (!isset($this->oOptions[$opt_id])) {
            $oOption = new stdClass();
            $oOption->id = $opt_id;
            $oOption->value = $opt_value;
            $this->oOptions[$opt_id] = $oOption;
        }
    }

}
