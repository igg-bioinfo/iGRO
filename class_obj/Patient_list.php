<?php

class Patient_list {

    public static $has_filter_center = false;
    public static $has_filter_pt_code = false;
    public static $has_filter_max_rows = false;
    public static $filter_oCenter = NULL;
    public static $filter_max_rows = 0;
    private static $max_rows_list = [
        [0, 'All'], [50, '50 rows'], [100, '100 rows'], [500, '500 rows'], [1000, '1000 rows'], [2000, '2000 rows'], [5000, '5000 rows']
    ];
    private static $filter_pt_code = '';
    private static $filter_prj_pt_code = '';
    private static $sql_filter = "";
    private static $sql_params = [];
    private static $sql_user_prot_ids = "0";

    const GET_FILTER_CENTER = 'fcid';
    const GET_FILTER_PT_CODE = 'fptc';
    const GET_FILTER_MAX_ROWS = 'fmr';

    //-----------------------------------------------------PUBLIC
    static function get_all($id_paz = 0, Paging_table $paging_table = null) {
        self::set_sql_filter();
        $select = " SELECT DISTINCT P.* ";
        $from = " FROM " . Patient::get_default_select() . " P ";
        if (is_array($id_paz)) {
            $where = ' WHERE P.id_paz IN (' . join(',', $id_paz) . ')' . self::$sql_filter;
        }
        else {
            $where = " WHERE 1 = 1 " . ($id_paz != 0 ? ' AND P.id_paz = ' . $id_paz : '') . self::$sql_filter;
        }

        if (is_null($paging_table)) {
            $orderby = " ORDER BY P.id_paz DESC";
            $patients = Database::read($select . $from . $where . $orderby, self::$sql_params);
        } else {
            $key = 'P.id_paz';
            $paging_table->add_order([["id_paz", "DESC"], ["patient_id", "ASC"]]);
            $patients = $paging_table->read($key, $select, $from, $where, self::$sql_params);
        }
        return self::get_oPatients($patients);
    }

    //-----------------------------------------------------FILTERS
    public static function draw_filters() {
        $html = '';
        $form = '';
        $js = '';
        self::manage_filter_post();
        self::manage_filter_get();
        if (self::$has_filter_center) {
            $oCenters = Center::get_all();
            $centers = [];
            foreach ($oCenters as $oCenter) {
                $centers[] = [0 => $oCenter->id, 1 => $oCenter->code];
            }
            $form .= Form_input::createSelect('filter_center_id', 'Center', $centers, isset(self::$filter_oCenter) && self::$filter_oCenter->id != 0 ? self::$filter_oCenter->id : '', 2, false, "");
            $js .= '$("#filter_center_id").val("");';
        }
        if (self::$has_filter_pt_code) {
            $form .= Form_input::createInputText('filter_pt_code', Language::find('patient_code'), self::$filter_pt_code, 2, false, "", 150);
            $js .= '$("#filter_pt_code").val("");';
        }
        if (self::$has_filter_max_rows) {
            $form .= Form_input::createSelect('filter_max_rows', 'Max rows', self::$max_rows_list, self::$filter_max_rows, 2, false, "");
            $js .= '$("#filter_max_rows").val("");';
        }
        if ($form != '') {
            $form .= Form_input::createHidden('post_filter', 'OK');
            HTML::$js .= JS::set_func("filter_clear", $js . "page_validation('form_filter');");
            $html .= HTML::set_paragraph('Filters');
            $html .= HTML::set_form($form, 'form_filter');
            $html .= HTML::set_button(Icon::set_filter() . 'Filter', "page_validation('form_filter');");
            $html .= HTML::set_button(Icon::set_clear() . 'Clear', "filter_clear();");
            $html .= HTML::BR;
            $html .= HTML::BR;
        }
        return $html;
    }

    //-----------------------------------------------------PRIVATE
    private static function manage_filter_post() {
        if (Security::sanitize(INPUT_POST, 'post_filter') == 'OK') {
            $post_center = Security::sanitize(INPUT_POST, 'filter_center_id');
            if ($post_center != '') {
                URL::changeable_var_add(self::GET_FILTER_CENTER, $post_center);
            } else {
                URL::changeable_var_remove(self::GET_FILTER_CENTER);
            }
            $post_pt_code = Security::sanitize(INPUT_POST, 'filter_pt_code');
            if ($post_pt_code != '') {
                URL::changeable_var_add(self::GET_FILTER_PT_CODE, $post_pt_code);
            } else {
                URL::changeable_var_remove(self::GET_FILTER_PT_CODE);
            }
            $post_max_rows = Security::sanitize(INPUT_POST, 'filter_max_rows');
            if ($post_max_rows != '') {
                URL::changeable_var_add(self::GET_FILTER_MAX_ROWS, $post_max_rows);
            } else {
                URL::changeable_var_remove(self::GET_FILTER_MAX_ROWS);
            }
            URL::redirect('');
        }
    }

    private static function manage_filter_get() {
        $get_center = URL::get_onload_var(self::GET_FILTER_CENTER);
        if ($get_center != '') {
            $oCenter = new Center();
            $oCenter->id = $get_center;
            self::$filter_oCenter = $oCenter;
        }
        $get_pt_code = URL::get_onload_var(self::GET_FILTER_PT_CODE);
        if ($get_pt_code != '') {
            self::$filter_pt_code = $get_pt_code;
        }
        $get_max_rows = URL::get_onload_var(self::GET_FILTER_MAX_ROWS);
        if ($get_max_rows != '') {
            self::$filter_max_rows = $get_max_rows;
        }
    }

    private static function get_oPatients($patients) {
        $oPaz = NULL;
        $oPatients = [];
        foreach ($patients as $patient) {
            $oPaz = new Patient();
            $oPaz->set_by_row($patient);
            $oPatients[] = $oPaz;
        }
        return $oPatients;
    }

    private static function set_sql_filter() {
        self::$sql_filter = '';
        self::$sql_params = [];
        if (isset(self::$filter_oCenter) && self::$filter_oCenter->id != 0) {
            self::$sql_filter .= " AND P.id_center = ? ";
            self::$sql_params[] = self::$filter_oCenter->id;
        }
        if (isset(self::$filter_pt_code) && self::$filter_pt_code != '') {
            self::$sql_filter .= " AND P.patient_id LIKE ? ";
            self::$sql_params[] = '%' . self::$filter_pt_code . '%';
        }
    }

}
