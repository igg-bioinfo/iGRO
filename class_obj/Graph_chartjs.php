<?php

class Graph_chartjs {

    //-------------------------------------------VARIABLES
    private $id = '';
    private $type = '';
    private $height = '';
    public $oDatasets = [];
    public $oXoption = NULL;
    public $oYoption = NULL;
    private $cutoffs = '';
    private $color_array = ['255, 99, 132', '54, 162, 235', '255, 205, 86', '153, 102, 255', '75, 192, 192', '255, 159, 64', '201, 203, 207'];
    private $color_i = 0;

    const TYPE_LINE = 'line';
    const AXIS_X = 'x';
    const AXIS_Y = 'y';
    const OPT_TYPE_DEFAULT = 'default';
    const OPT_TYPE_DATE = 'date';
    const OPT_TYPE_NUMBER = 'number';

    //-------------------------------------------CONSTRUCT
    function __construct($id, $type = self::TYPE_LINE, $height = 400, $color_array = []) {
        HTML::add_link('vendor/charts.min', 'js');
        $this->id = $id;
        $this->type = $type;
        $this->height = $height;
        if (count($color_array)) {
            $this->color_array = $color_array;
        }
        $this->oXoption = $this->add_option();
        $this->oYoption = $this->add_option();
    }

    //-------------------------------------------PUBLIC
    function draw() {
        $this->set_cutoff_registration();
        $this->set_config();
        HTML::$js_onload .= ' var ctx_' . $this->id . ' = document.getElementById("canvas_' . $this->id . '").getContext("2d"); ';
        //HTML::$js_onload .= ' document.getElementById("canvas_' . $this->id . '").height = ' . $this->height . '; ';
        HTML::$js_onload .= ' window.my' . $this->id . ' = new Chart(ctx_' . $this->id . ', config_' . $this->id . '); ';
        return '<canvas id="canvas_' . $this->id . '" style="height: ' . $this->height . 'px; width: 100%;"></canvas>'; //style="height: ' . $this->height . 'px; width: 100%;"
    }

    function add_dataset_row($id_dataset, $label, $x, $y) {
        if (!isset($this->oDatasets[$id_dataset])) {
            $oDataset = new stdClass();
            $oDataset->label = $label;
            $oDataset->fill = false;
            $oDataset->show = false;
            $oDataset->rows = [];
            $this->oDatasets[$id_dataset] = $oDataset;
        }
        $this->set_row($id_dataset, $x, $y);
    }

    function add_cutoff($label, $value, $axis = self::AXIS_Y, $color = '') {
        $this->cutoffs .= '{ ';
        $this->cutoffs .= '"' . $axis . '": ' . $value . ', ';
        if ($color . '' != '') {
            $this->cutoffs .= '"style": "rgba(' . $color . ')", ';
        }
        $this->cutoffs .= '"text": "' . $label . '", ';
        $this->cutoffs .= '}, ';
    }

    //-------------------------------------------PRIVATE
    private function set_row($id, $x, $y) {
        if (isset($this->oDatasets[$id])) {
            $this->oDatasets[$id]->rows[] = '{ x: ' . (is_numeric($x) ? $x : '\'' . $x . '\'') . ', y: ' . (is_numeric($y) ? $y : '\'' . $y . '\'') . ' },';
        }
    }

    private function set_cutoff_registration() {
        if ($this->cutoffs . '' != '' && !Strings::contains(HTML::$js, 'cutoffPlugin')) {
            HTML::$js .= 'var cutoffPlugin = { afterDraw: function(chartInstance) {
                var yScale = chartInstance.scales["y-axis-0"]; var canvas = chartInstance.chart; var ctx = canvas.ctx; var index; var line; var style;
                if (chartInstance.options.cutoffLine) {
                    for (index = 0; index < chartInstance.options.cutoffLine.length; index++) {
                        line = chartInstance.options.cutoffLine[index];
                        style = (!line.style ? "rgba(3,3,3, .6)" : line.style);
                        yValue = (line.y ? yScale.getPixelForValue(line.y) : 0);
                        ctx.lineWidth = 1;
                        if (yValue) {
                            ctx.beginPath(); ctx.moveTo(0, yValue); ctx.lineTo(canvas.width, yValue); ctx.strokeStyle = style; ctx.setLineDash([5, 5]);
                            ctx.stroke(); ctx.setLineDash([]);
                        }
                        if (line.text) { 
                            ctx.fillStyle = style; ctx.fillText(line.text, Math.round((chartInstance.chart.width - ctx.measureText(line.text).width) / 2), yValue + ctx.lineWidth + 10); }
                        } 
                        return; 
                }; } }; Chart.pluginService.register(cutoffPlugin);';
        }
    }

    private function set_config() {
        HTML::$js .= 'var config_' . $this->id . ' = { ';
        HTML::$js .= 'type: "' . $this->type . '", ';
        HTML::$js .= 'data: { datasets: [ ';
        foreach ($this->oDatasets as $oDataset) {
            HTML::$js .= $this->set_dataset($oDataset);
        }
        HTML::$js .= ' ] }, ';
        HTML::$js .= 'options: { ';
        //HTML::$js .= 'maintainAspectRatio: false, ';
        //HTML::$js .= 'responsive: false, ';
        //HTML::$js .= 'height: ' . $this->height . ', ';
        HTML::$js .= 'legend: { display: true, position: "top", }, ';
        if ($this->cutoffs . '' != '') {
            HTML::$js .= ' "cutoffLine": [ ' . $this->cutoffs . ' ], ';
        }
        HTML::$js .= 'scales: { xAxes: [ ';
        HTML::$js .= $this->set_option($this->oXoption);
        HTML::$js .= ' ], yAxes: [ ';
        HTML::$js .= $this->set_option($this->oYoption);
        HTML::$js .= ' ], }, }, }; ';
    }

    private function add_option() {
        $oOption = new stdClass();
        $oOption->label = '';
        $oOption->type = self::OPT_TYPE_NUMBER;
        $oOption->format = '';
        $oOption->round = '';
        $oOption->tick_start_0 = false;
        $oOption->tick_max = 0;
        $oOption->tick_max_limit = 0;
        return $oOption;
    }

    private function set_option($oOption) {
        $html = "{ ";
        if ($oOption->label . '' != '') {
            $html .= ' scaleLabel: { display: true, labelString: "' . $oOption->label . '" }, ';
        }

        if ($oOption->type == self::OPT_TYPE_DATE) {
            $html .= 'type: "time", time: { format: "YYYY/MM/DD", round: "' . $oOption->round . '", tooltipFormat: "ll",
                displayFormats: {
                   "millisecond": "' . $oOption->format . '", "second": "' . $oOption->format . '", "minute": "' . $oOption->format . '", "hour": "' . $oOption->format . '",
                   "day": "' . $oOption->format . '", "week": "' . $oOption->format . '", "month": "' . $oOption->format . '", "quarter": "' . $oOption->format . '","year": "' . $oOption->format . '"
                }
            },';
        }
        if ($oOption->type == self::OPT_TYPE_NUMBER) {
            $ticks = '';
            if ($oOption->tick_start_0) {
                $ticks .= 'beginAtZero: true, ';
            }
            if ($oOption->tick_max > 0) {
                $ticks .= 'max: ' . $oOption->tick_max . ', ';
            }
            if ($oOption->tick_max_limit > 0) {
                $ticks .= 'maxTicksLimit: ' . $oOption->tick_max_limit . ', ';
            }
            if ($ticks) {
                $html .= 'ticks: { ' . $ticks . '}, ';
            }
        } else {
            $html .= 'position: "left", ticks: { reverse: true }, ';
        }
        $html .= "}, ";
        return $html;
    }

    private function set_dataset($oDataset) {
        $html = "{ ";
        $html .= "label: '" . $oDataset->label . "', ";
        $html .= "backgroundColor: 'rgb(" . $this->color_array[$this->color_i] . ")', ";
        $html .= "borderColor: 'rgb(" . $this->color_array[$this->color_i] . ")', ";
        $html .= "fill: " . ($oDataset->fill ? "true" : "false") . ", ";
        $html .= "data: [ ";
        foreach ($oDataset->rows as $row) {
            $html .= $row;
        }
        $html .= "], ";
        if (!$oDataset->show) {
            $html .= "hidden: true, ";
        }
        $html .= "}, ";
        $this->color_i = $this->color_i == count($this->color_array) - 1 ? 0 : $this->color_i + 1;
        return $html;
    }

}
