<?php

class Paging_table {

    const ENTRIES_NUMBER_LIST = [
        [10, '10'], [25, '25'], [50, '50'], [100, '100'], [500, '500']
    ];
    const TYPE_SELECT = 6;
    const TYPE_MULTISELECT = 7;
    const TYPE_RADIO = 8;
    const TYPE_RANGE = 9;

    private $limit = 50;
    private $page = 1;
    private $total_records;
    private $id;
    private $limit_id;
    private $page_id;
    private $order_id;
    private $column_mapping;
    private $column_mapping_id;
    private $order_enable = [];
    private $filter = [];
    public $params = [];
    private $orders = [];

    /* COLUMN MAPPING FIELDS
     * id : name of the field on the table (MANDATORY)
     * label : label on the filter (MANDATORY)
     * type : type of the field (MANDATORY)
     * values : values for the the dropdown for type SELECT (OPTIONAL)
     * orderable : specify if column can be order (MANDATORY)
     * order_by_id : name of the field on the table for order (OPTIONAL)
     * default_value : default value for the filter (OPTIONAL)
     */

    function __construct($id, $column_mapping, $extra_filters = []) {
        $this->id = $id;
        $this->limit_id = $id . '_limit';
        $this->page_id = $id . '_page';
        $this->order_id = $id . '_order';
        $this->column_mapping = $column_mapping;
        $this->column_mapping_id = $id . '_colmapping';

        $this->add_extra_filters($extra_filters);

        $this->init_filters_query();

        if (Security::sanitize(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            // URL::changeable_vars_reset();
            URL::changeable_var_remove('pg');

            if (!is_null(Security::sanitize(INPUT_POST, $this->order_id)) && !empty(Security::sanitize(INPUT_POST, $this->order_id))) {
                $this->orders = json_decode(Security::sanitize(INPUT_POST, $this->order_id));
                $this->orders = array_map([$this, 'map_orders'], $this->orders);
            }
            if (!is_null(Security::sanitize(INPUT_POST, $this->limit_id))) {
                $this->limit = Security::sanitize(INPUT_POST, $this->limit_id);
            }
            if (!is_null(Security::sanitize(INPUT_POST, $this->page_id))) {
                $this->page = Security::sanitize(INPUT_POST, $this->page_id);
                URL::changeable_var_add('pg_nmbr', $this->page);
            }

            // create filter query
            foreach ($this->column_mapping as $index => $column) {
                $post_value = Security::sanitize(INPUT_POST, $this->id . '_' . str_replace('.', '_', $column["id"]));
                if ($column) {
                    $type = isset($column["type"]) ? $column["type"] : 'string';
                    if (isset($column["is_extra"])) {
                        $this->add_extra_filter_clause($column, $post_value);
                    } else {
                        if ($type == Paging_table::TYPE_MULTISELECT) {
                            $post_value = Security::sanitize(INPUT_POST, $this->id . '_' . str_replace('.', '_', $column["id"]), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                        }
                        $this->add_filter_clause($column["id"], $type, $post_value, Security::sanitize(INPUT_POST, $this->id . '_' . str_replace('.', '_', $column["id"]) . '_to'));
                    }
                }
            }

            // clear filter
            if (!is_null(Security::sanitize(INPUT_POST, $this->id . '_post_act'))) {
                if (Security::sanitize(INPUT_POST, $this->id . '_post_act') == 'clear_filter') {
                    $this->init_filters_query();
                    $this->orders = [];
                }
            }

            // put object in session
            $_SESSION[URL::$prefix . $this->id] = serialize($this);
            global $page_url;
            URL::redirect($page_url);
        } else {
            if (empty($this->order_enable)) {
                foreach ($this->column_mapping as $index => $column) {
                    if (isset($column["orderable"]) && $column["orderable"]) {
                        $this->order_enable[] = $index;
                    }
                }
            }

            if (!empty(URL::get_onload_var('pg_nmbr'))) {
                $this->page = (int) URL::get_onload_var('pg_nmbr');
            }
        }

        // if object is session get object
        if (isset($_SESSION[URL::$prefix . $this->id])) {
            $prev_state = unserialize($_SESSION[URL::$prefix . $this->id]);
            $reflect = new ReflectionClass($this);
            $props = $reflect->getProperties();
            foreach ($props as $prop) {
                if ($prop->getName() !== 'page') {
                    $this->{$prop->getName()} = $prev_state->{$prop->getName()};
                }
            }
        }
    }

    // render table
    public function set($content, $javascript = '', $class = '', $style = '', $other = '') {
        HTML::add_link('vendor/datatables.min', 'css');
        HTML::add_link('vendor/responsive.datatables.min', 'css');
        HTML::add_link('responsive.datatables-site', 'css');
        HTML::add_link('vendor/datatables.min', 'js');
        HTML::add_link('vendor/datatables.responsive.min', 'js');
        HTML::add_link('vendor/date-dd-MMM-yyyy', 'js');
        HTML::add_link('paging', 'js');

        HTML::$js_onload .= '$("#' . $this->id . '").DataTable( {'
                . (empty($javascript) ? '' : $javascript . ',')
                . '"responsive": true,'
                . '"paging": false,'
                . '"info": false,'
                . '"searching": false,'
                . '"ordering": false,'
                . '"headerCallback": header_callback'
                . '});';
        $class = 'display responsive nowrap' . $class;
        $style = ' style="width:100%; ' . (Language::$is_right_dir ? 'direction: rtl' : '') . ' ' . $style . '" ';
        return $this->set_filter_header()
                . HTML::set_table($content, $this->id, $style . $other, $class)
                . $this->set_footer();
    }

    // render footer
    private function set_footer() {
        $entries_number_select = '<select name="' . $this->limit_id . '" id="' . $this->limit_id . '" style="border-radius:4px;border:1px solid #AAAAAA;" onchange="$(\'#form1\').submit();">';

        foreach (self::ENTRIES_NUMBER_LIST as $entries_number) {
            $entries_number_select .= '<option value="' . $entries_number[0] . '" ' . ($this->limit == $entries_number[0] ? "selected='selected'" : "") . '>' . $entries_number[1] . '</option>';
        }
        $entries_number_select .= '</select>';

        $max_page_number = ceil($this->total_records / $this->limit);

        $page_number_select = '<select name="' . $this->page_id . '_select" id="' . $this->page_id . '_select" style="border-radius:4px;border:1px solid #AAAAAA;" onchange="' . JS::call_func('select_page', [$this->page_id]) . '">';
        for ($i = 0; $i < $max_page_number; $i++) {
            $page_number_select .= '<option value="' . ( $i + 1 ) . '" ' . ($this->page == ( $i + 1 ) ? "selected='selected'" : "") . '>' . ( $i + 1 ) . '</option>';
        }
        $page_number_select .= '</select>';

        $back_page = '<button class="btn btn-link" onclick="' . JS::call_func('back', [$this->page_id]) . '">' . Icon::set('backward', 1) . '</button>';
        $next_page = '<button class="btn btn-link" onclick="' . JS::call_func('next', [$this->page_id, $max_page_number]) . '">' . Icon::set('forward', 1) . '</button>';
        $show_text = Language::find('DT_info2');
        $show_text = str_replace('_MENU_', $entries_number_select, $show_text);
        $show_text = str_replace('_TOTAL_',  ($this->total_records ?: '0'), $show_text);
        $tds = HTML::set_td($show_text)
                . HTML::set_td($back_page . $page_number_select . $next_page, '', false, 'text-right');

        return HTML::set_table(HTML::set_tr($tds), '', '', 'table')
                . Form_input::createHidden($this->order_id, empty($this->orders) ? '' : json_encode($this->orders))
                . Form_input::createHidden($this->page_id, $this->page)
                . Form_input::createHidden($this->column_mapping_id, json_encode($this->order_enable));
    }

    // for future use
    private static function set_header() {
        
    }

    // render filter
    private function set_filter_header() {
        $row = '';
        $extra_row = '';
        foreach ($this->column_mapping as $column) {
            if ($column) {
                $input_id = $this->id . '_' . str_replace('.', '_', $column["id"]);

                if (isset($column["is_extra"])) {
                    $extra_row .= Form_input::createLabel($this->id . '_' . $column["id"], $column["label"], false, [2, 4], 'pb-4');

                    if (is_array($column["clause"])) {
                        $cell = '';
                        $post_value = isset($this->filter[$column["id"]]) ? $this->filter[$column["id"]] : null;
                        foreach ($column["clause"] as $index => $radio_value) {
                            if ($post_value === $radio_value[0]) {
                                $post_value = $index;
                            }

                            $cell .= HTML::set_bootstrap_cell(Form_input::createRadioBasic($input_id, $radio_value[1], $post_value, $index, ''), ' d-flex col');
                        }

                        $extra_row .= HTML::set_bootstrap_cell(HTML::set_row($cell), '8 col-lg-4');
                    } else {
                        $post_value = isset($this->filter[$column["id"]]);
                        $extra_row .= Form_input::createCheckbox($input_id, '', $post_value, [4, 8], false, '', false, 'm-0', '', true);
                    }
                } else {
                    $row .= Form_input::createLabel($this->id . '_' . $column["id"], $column["label"], false, [2, 4], 'pb-4');
                    $type = isset($column["type"]) ? $column["type"] : 'string';
                    $post_value = isset($this->filter[$column["id"]]) && isset($this->params[$column["id"]]) ? $this->params[$column["id"]] : '';

                    switch ($type) {
                        case Field::TYPE_DATE:
                            $input_id_to = $input_id . '_to';
                            $post_value_to = $this->params[$column["id"] . '_to'] ?? '';

                            $date_from_col = HTML::set_text('From:', false, '', 'pr-2')
                                    . Form_input::createDatePickerBasic($input_id, $post_value, JS::call_func('check_date_range', [$input_id, false, null, $input_id_to]));

                            $date_to_col = HTML::set_text('To:', false, '', 'pr-2')
                                    . Form_input::createDatePickerBasic($input_id_to, $post_value_to, JS::call_func('check_date_range', [$input_id_to, false, $input_id, null]));

                            $row .= HTML::set_bootstrap_cell($date_from_col, '4 col-lg-2', false, 'd-flex')
                                    . HTML::set_bootstrap_cell($date_to_col, '4 col-lg-2', false, 'd-flex');
                            break;
                        case Field::TYPE_BOOL:
                            $row .= Form_input::createCheckbox($input_id, '', $post_value, [4, 8], false, '', false, 'm-0', '', true);
                            break;
                        case Field::TYPE_INT:
                            $row .= HTML::set_bootstrap_cell(Form_input::createInputTextBasic($input_id, $post_value, JS::call_func('check_number', [$input_id, false]), 255), '8 col-lg-4');
                            break;
                        case self::TYPE_SELECT:
                            $row .= HTML::set_bootstrap_cell(Form_input::createSelectBasic($input_id, $column["values"], $post_value, ''), '8 col-lg-4');
                            break;
                        case self::TYPE_MULTISELECT:
                            if (is_null($this->filter[$column["id"]])) { // filter has not been set yet
                                $post_value = $column["default_value"];
                            }
                            if (!is_array($post_value)) {
                                $post_value = [];
                            }
                            $row .= Form_input::createMultiSelect($input_id, '', $column["values"], $post_value, [4, 8], false, '');
                            break;
                        case self::TYPE_RADIO:
                            $cell = '';
                            foreach ($column["values"] as $radio_value) {
                                $cell .= HTML::set_bootstrap_cell(Form_input::createRadioBasic($input_id, $radio_value[1], $post_value, $radio_value[0], ''), ' d-flex col');
                            }
                            $row .= HTML::set_bootstrap_cell(HTML::set_row($cell), '8 col-lg-4');
                            break;
                        case self::TYPE_RANGE:
                            $input_id_to = $input_id . '_to';
                            $post_value_to = $this->params[$column["id"] . '_to'] ?? '';

                            $col_from = HTML::set_text('From:', false, '', 'pr-2')
                                    . Form_input::createInputTextBasic($input_id, $post_value, JS::call_func('check_number_min_max_by_id', [$input_id, $input_id, $input_id_to, false]), 255);

                            $col_to = HTML::set_text('To:', false, '', 'pr-2')
                                    . Form_input::createInputTextBasic($input_id_to, $post_value_to, JS::call_func('check_number_min_max_by_id', [$input_id_to, $input_id, $input_id_to, false]), 255);

                            $cell = HTML::set_bootstrap_cell($col_from, '6 col-lg-6', false, 'd-flex')
                                    . HTML::set_bootstrap_cell($col_to, '6 col-lg-6', false, 'd-flex');

                            $row .= HTML::set_bootstrap_cell(HTML::set_row($cell), '8 col-lg-4');
                            break;
                        case Field::TYPE_STRING:
                        default:
                            $row .= HTML::set_bootstrap_cell(Form_input::createInputTextBasic($input_id, str_replace('%', '', $post_value), '', 255), '8 col-lg-4');
                            break;
                    }
                }
            }
        }

        $is_collapsed = $this->is_filter_default();
        $filter = '<div class="card card-body">'
            . '<a class="collapse-panel' . ($is_collapsed ? ' collapsed' : '') . '" data-toggle="collapse" href="#' . $this->id . '_filters" role="button" aria-expanded="' . ($is_collapsed ? 'false' : 'true') . '" aria-controls="' . $this->id . '_filters">'
            . '<i class="fa fa-fw fa-chevron-right"></i>'
            . '<i class="fa fa-fw fa-chevron-down"></i>'
            . Language::find('filter')
            . '</a>'
            . '<div class="collapse' . ($is_collapsed ? '' : ' show') . '" id="' . $this->id . '_filters">'
            . Form_input::br(false, true)
            . HTML::set_row($row);

        if (!empty($extra_row)) {
            $filter .= Form_input::br(false, true) . HTML::set_row($extra_row);
        }

        $filter .= HTML::set_row(HTML::set_bootstrap_cell(HTML::set_button(Icon::set('filter', 1) . Language::find('filter'), "filter_validation()")
             . HTML::set_button(Icon::set_clear() . Language::find('clear'), "create_hidden_field('" . $this->id . "_post_act', 'clear_filter'); $('#form1').submit();"), 12, false, 'text-right'))
            . '<div class="alert alert-danger m-2" role="alert" style="display: none">' . Language::find('error_present', ['validation']) . '</div>'
            . '</div>'
            . '</div>'
            . Form_input::br(true);

        return $filter;
    }

    public function add_order($orders) {
        if (!is_array($orders)) {
            throw new Exception('Parameter must be an array!');
        }

        foreach ($orders as $order) {
            if (is_array($order)) {
                $is_already_in_array = $this->filter_orders($this->orders, $order[0]);
                if (!count($is_already_in_array)) {
                    $this->orders[] = [$order[0], (isset($order[1]) ? strtolower($order[1]) : 'desc')];
                }
            } else {
                $is_already_in_array = $this->filter_orders($this->orders, $order);
                if (!count($is_already_in_array)) {
                    $this->orders[] = [$order, 'desc'];
                }
            }
        }
    }

    private function map_orders($value) {
        if (is_int($value[0])) {
            if (isset($this->column_mapping[$value[0]]["order_by_id"])) {
                $value[] = $this->column_mapping[$value[0]]["order_by_id"];
                return $value;
            }
            $value[] = $this->column_mapping[$value[0]]["id"];
        }
        return $value;
    }

    private function filter_orders($orders, $order) {
        return array_filter($orders, function($value) use($order) {
            if (isset($value[2])) {
                return $value[2] === $order;
            }
            return $value[0] === $order;
        });
    }

    private function add_extra_filters($extra_filters) {
        if (!is_array($extra_filters)) {
            throw new Exception('Parameter must be an array!');
        }

        foreach ($extra_filters as $extra_filter) {
            $extra_filter["is_extra"] = 1;
            $this->column_mapping[] = $extra_filter;
        }
    }

    private function add_filter_clause($id, $type, $value, $value_to = null) {
        if ((is_null($value) || empty($value)) && !in_array($type, [Field::TYPE_BOOL, self::TYPE_MULTISELECT, self::TYPE_RADIO, Field::TYPE_DATE, self::TYPE_RANGE])) {
            return;
        }

        switch ($type) {
            case Field::TYPE_DATE:
                if ($value) {
                    $this->params[$id] = $value;
                }
                if ($value_to) {
                    $this->params[$id . '_to'] = $value_to;
                }
                if ($value && $value_to) {
                    $this->filter[$id] = $id . " BETWEEN ? AND ?";
                } else if ($value) {
                    $this->filter[$id] = $id . " >= ?";
                } else if ($value_to) {
                    $this->filter[$id] = $id . " <= ?";
                }
                // $this->filter[$id] = $id . " BETWEEN ? AND " . ($value_to ? "?" : 'NOW()' ) . "";
                break;
            case Field::TYPE_BOOL:
                if ($value) {
                    $this->params[$id] = $value;
                    $this->filter[$id] = $id . " = ?";
                } else {
                    unset($this->filter[$id]);
                }
                break;
            case self::TYPE_SELECT:
            case Field::TYPE_INT:
                $this->params[$id] = $value;
                $this->filter[$id] = $id . " = ?";
                break;
            case self::TYPE_MULTISELECT:
                $this->params[$id] = $value;
                if (is_null($value) || !is_array($value)) {
                    $this->filter[$id] = "1 = ?";
                } else {
                    if (in_array('NULL', $value)) {
                        $this->filter[$id] = "( " . $id . " IS NULL";
                        unset($value[array_search('NULL', $value)]);
                        if (count($value)) {
                            $this->filter[$id] .= " OR " . $id . " IN (" . join(', ', $value) . ")";
                        }
                        $this->filter[$id] .= " )";
                    } else {
                        $this->filter[$id] = $id . " IN (" . join(', ', $value) . ")";
                    }
                }
                break;
            case self::TYPE_RADIO:
                if ($value !== '') {
                    $this->params[$id] = $value;
                    $this->filter[$id] = $id . " = ?";
                } else {
                    unset($this->filter[$id]);
                }
                break;
            case self::TYPE_RANGE:
                if ($value) {
                    $this->params[$id] = $value;
                }
                if ($value_to) {
                    $this->params[$id . '_to'] = $value_to;
                }
                if ($value && $value_to) {
                    $this->filter[$id] = $id . " BETWEEN ? AND ?";
                } else if ($value) {
                    $this->filter[$id] = $id . " >= ?";
                } else if ($value_to) {
                    $this->filter[$id] = $id . " <= ?";
                }

                break;
            case Field::TYPE_STRING:
            default:
                $this->params[$id] = '%' . $value . '%';
                $this->filter[$id] = $id . " LIKE ?";
                break;
        }
    }

    private function add_extra_filter_clause($filter, $value) {
        if ($value) {
            if (is_array($filter["clause"])) {
                $this->filter[$filter["id"]] = $filter["clause"][$value][0];
            } else {
                $this->filter[$filter["id"]] = $filter["clause"];
            }
        } else {
            unset($this->filter[$filter["id"]]);
        }
    }

    private function is_filter_default() {
        foreach ($this->column_mapping as $column) {
            if (isset($column["type"])) {
                switch ($column["type"]) {
                    case self::TYPE_MULTISELECT:
                        foreach ($column["default_value"] as $value) {
                            if (!Strings::contains($this->filter[$column["id"]], $value)) {
                                return false;
                            }
                        }
                        break;
                    default:
                        if (isset($this->filter[$column["id"]]) && $this->filter[$column["id"]] !== ($column["default_value"] ?? null)) {
                            return false;
                        }
                        break;
                }
            }

            if (isset($column["is_extra"])) {
                if (!empty($this->filter[$column["id"]])) {
                    return false;
                }
            }
        }

        return true;
    }

    // init filter query with default values
    private function init_filters_query() {
        foreach ($this->column_mapping as $index => $column) {
            if (isset($column["orderable"]) && $column["orderable"]) {
                $this->order_enable[] = $index;
            }
            if ($column && isset($column["default_value"])) {
                $value = $column["default_value"];
                $type = isset($column["type"]) ? $column["type"] : 'string';
                if (isset($column["is_extra"])) {
                    $this->add_extra_filter_clause($column, $value);
                } else {
                    $this->add_filter_clause($column["id"], $type, $value, isset($column["default_value"]) ? $column["default_value"] : null);
                }
            } else {
                unset($this->filter[$column["id"]]);
                unset($this->params[$column["id"]]);

                if (in_array($column["type"], [Field::TYPE_DATE, self::TYPE_RANGE])) {
                    unset($this->params[$column["id"] . '_to']);
                }
            }
        }
    }

    public function read($key, $select, $from, $where, $params, $decryption_key = [], $db = '') {
        if (!empty($this->filter)) {
            if (empty($where)) {
                $where = ' WHERE ' . join(' AND ', $this->filter);
            } else {
                $where .= ' AND ' . join(' AND ', $this->filter);
            }
            foreach ($this->params as $param) {
                if (!is_array($param)) {
                    $params[] = $param;
                }
            }
        }

        $view = $key;
        if (Strings::contains($key, '.')) {
            $key = explode('.', $key)[1];
        }

        $count_query = 'SELECT COUNT(DISTINCT(' . $key . '))'
                . ' FROM ('
                . $select
                . $from
                . $where
                . ' ) D ';

        $start = microtime(true);
        $this->total_records = Database::read($count_query, $params, $db)[0][0];
        $time_elapsed_secs = microtime(true) - $start;
        HTML::$debug_info .= 'COUNT QUERY: ' . $time_elapsed_secs . 'sec<br/>';

        if ($this->page > ceil($this->total_records / $this->limit)) {
            $this->page = ceil($this->total_records / $this->limit) ?: 1;
        }

        if (is_null($this->total_records) || !$this->total_records) {
            return [];
        }

        if (!empty($this->orders)) {
            $orders = [];
            foreach ($this->orders as $order) {
                if (is_int($order[0])) {
                    if (!isset($this->column_mapping[$order[0]])) {
                        continue;
                    }
                    if (isset($this->column_mapping[$order[0]]["order_by_id"])) {
                        $orders[] = $this->column_mapping[$order[0]]["order_by_id"] . ' ' . $order[1];
                    } else {
                        $orders[] = $this->column_mapping[$order[0]]["id"] . ' ' . $order[1];
                    }
                } else {
                    $orders[] = $order[0] . ' ' . $order[1];
                }
            }
            $orderby = " ORDER BY " . join(', ', $orders);
        } else {
            $orderby = " ORDER BY " . $key . " desc";
        }

        HTML::$debug_info .= $orderby . '<br/>';

        $min_row_num = ($this->limit * ($this->page - 1)) + 1;
        $max_row_num = $min_row_num + $this->limit;

        $key_row_numbers = " SELECT " . $key . ", ROW_NUMBER() OVER (" . $orderby . ") as RowNum FROM ("
            . $select . ", ROW_NUMBER() OVER (PARTITION BY " . $view . " ORDER BY " . $view . ") as RowDup"
            . $from
            . $where
            . " ) DK "
            . " WHERE RowDup = 1";

        $sql = "SELECT T_FINAL.* FROM ("
            . $select
            . $from
            . $where
            . " ) T_FINAL"
            . " INNER JOIN ("
            . $key_row_numbers
            . " ) KRN"
            . " ON T_FINAL." . $key . " = KRN." . $key
            . " WHERE RowNum >= " . $min_row_num . " AND RowNum < " . $max_row_num
            . $orderby;

        if (!empty($params)) {
            array_push($params, ...$params);
        }
        $start = microtime(true);
        $res = Database::read($sql, $params, $db);
        $time_elapsed_secs = microtime(true) - $start;
        HTML::$debug_info .= 'SELECT QUERY: ' . $time_elapsed_secs . 'sec<br/>';

        return self::group_by_key_check($res, $key);
    }

    private static function group_by_key_check($res, $primary_key) {
        $keys = array_column($res, $primary_key);

        foreach ($keys as $index => $key) {
            $items_pos = array_keys($keys, $key);

            if (count($items_pos) > 1) {
                $i = array_search($index, $items_pos);
                if ($i > 0 && ($items_pos[$i] - $items_pos[$i - 1] > 1)) {
                    self::move_element($res, $items_pos[$i], $items_pos[$i - 1] + 1);
                }
            }
        }

        return $res;
    }

    private static function move_element(&$array, $a, $b) {
        $out = array_splice($array, $a, 1);
        array_splice($array, $b, 0, $out);
    }
}
