<?php

class Form_input {

    const LG_SPACE_PREFIX = 'col-lg-';
    const MD_SPACE_PREFIX = 'col-md-';
    const SM_SPACE_PREFIX = 'col-sm-';
    const XS_SPACE_PREFIX = 'col-xs-';

    public static $check_js_onload_list = array();

    public static function set_normal_html($html) {
        $text = '';
        $text .= '</div>';
        $text .= '<p style="padding-left: 10px;">';
        $text .= $html;
        $text .= '</p>';
        $text .= '<div class="row">';
        return $text;
    }

    public static function br($is_double = false, $insert_line = false, $is_view = false) {
        $html = '';
        if (!$is_view) {
            $html = '<div class="w-100">' . ($is_double ? HTML::BR : '') . ($insert_line ? HTML::HR : '') . '</div>';
        } else {
            $html = ($is_double ? HTML::BR : '') . ($insert_line ? HTML::HR : '');
        }
        return $html;
    }

    public static function addOnLoadScript($validate) {
        if (!empty($validate) && !in_array($validate, self::$check_js_onload_list)) {
            HTML::$js_onload .= 'try { '
                    . $validate
                    . ' } catch (err) { '
                    . 'console.error(err.stack); '
                    . 'handle_javascript_errors_on_page(); '
                    . '} ';
            self::$check_js_onload_list[] = $validate;
        }
    }

    public static function createLabel($id, $label, $is_view = false, $space_column = 12, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        return '<div ' . HTML::set_classes_and_styles([$space_column_classes], $custom_class, $custom_style) . '>'
                . HTML::set_label($id, $label, true)
                . '</div>';
    }

    public static function createRadio($name, $label, $value, $radiovalue, $space_column, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-check', 'form-check-inline', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!$is_view) {
            $html .= '<label class="form-check-label radio_container" >'
                    . '<input class="form-check-input" type="radio" id="' . $name . '_' . $radiovalue . '" name="' . $name . '" ' . ($radiovalue . '' == $value . '' ? 'checked' : '') . ' value="' . $radiovalue . '" onclick="' . $validate . '" />'
                    . '<span class="radio_checkmark"></span>'
                    . '' . $label
                    . '</label>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<i class="' . ($radiovalue . '' == $value . '' ? Globals::ICON_CHECKED : 'fa fa-circle-thin fa-2x ') . ' align-middle"></i>'
                    . '<span  class ="pl-2 align-middle" >' . $label . '</span>';
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createRadioBasic($name, $label, $value, $radiovalue, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $html = '';

        if (!$is_view) {
            $html .= '<label ' . HTML::set_classes_and_styles(['form-check-label', 'radio_container'], $custom_class, $custom_style) . '>'
                    . '<input class="form-check-input" type="radio" id="' . $name . '_' . $radiovalue . '" name="' . $name . '" ' . ($radiovalue . '' == $value . '' ? 'checked' : '') . ' value="' . $radiovalue . '" onclick="' . $validate . '" />'
                    . '<span class="radio_checkmark"></span>'
                    . '' . $label
                    . '</label>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<i ' . HTML::set_classes_and_styles([($radiovalue . '' == $value . '' ? Globals::ICON_CHECKED : 'fa fa-circle-thin fa-2x'), 'align-middle'], $custom_class, $custom_style) . ' ></i>'
                    . '<span class ="pl-2 align-middle" >' . $label . '</span>';
        }

        return $html;
    }

    public static function createCheckbox($name, $label, $value, $space_column, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '', $is_slider = false) {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-check', 'form-check-inline', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!$is_view) {
            $html .= '<label class="form-check-label ' . ($is_slider ? 'switch' : 'cb_container') . '">'
                    . '<input class="form-check-input" type="checkbox" id="' . $name . '" name="' . $name . '" ' . ($value . '' ? 'checked' : '') . ' value="1" onclick="' . $validate . '" />'
                    . '<span class="' . ($is_slider ? 'slider round' : 'checkmark') . '"></span>'
                    . '' . $label
                    . '</label>';

            self::addOnLoadScript($validate);
        } else {
            $html .= '<i class="' . ($value . '' == '1' ? Globals::ICON_CHECKED : 'fa fa-square-o fa-2x') . '"></i>'
                    . '' . $label;
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createCheckboxBasic($name, $label, $value, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $html = '';

        if (!$is_view) {
            $html .= '<label ' . HTML::set_classes_and_styles(['form-check-label', 'cb_container'], $custom_class, $custom_style) . '>'
                    . '<input class="form-check-input" type="checkbox" id="' . $name . '" name="' . $name . '" ' . ($value . '' ? 'checked' : '') . ' value="1" onclick="' . $validate . '" />'
                    . '<span class="checkmark"></span>'
                    . '' . $label
                    . '</label>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<i ' . HTML::set_classes_and_styles([($value . "" == "1" ? Globals::ICON_CHECKED : 'fa fa-square-o fa-2x'), 'align-middle'], $custom_class, $custom_style) . '></i>'
                    . '<span class ="pl-2 align-middle" >' . $label . '</span>';
        }

        return $html;
    }

    public static function createTextarea($id, $label, $value, $space_column, $rows, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if (!$is_view) {
            $html .= '<textarea class="form-control control-area" id="' . $id . '" name="' . $id . '" rows="' . $rows . '" maxlength="4000" onkeyup="' . $validate . '" onblur="' . $validate . '">' . $value . '</textarea>'
                    . '<div id="status_' . $id . '">'
                    . '<div id="error_' . $id . '">&nbsp;</div>'
                    . '</div>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<div id="status_' . $id . '" class="alert alert-light">' . nl2br($value) . '</div>';
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createTextEditor($id, $label, $value, $space_column, $height, $is_newline, $validate, $is_view = false, $config = '', $is_multi = false, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if (!$is_view) {
            $html .= '<textarea class="form-control control-area" id="' . $id . '" name="' . $id . '">' . ($value) . '</textarea>'
                    . '<div id="status_' . $id . '">'
                    . '<div id="error_' . $id . '">&nbsp;</div>'
                    . '</div>';
            $js_init = '';
            if (!$is_view && !$is_multi) {
                $js_init = '
                    CKEDITOR.replace( "' . $id . '", { height: ' . $height . ', ' . ($config != '' ? 'customConfig: "config_' . $config . '.js",' : '') . ' } );
                    CKEDITOR.instances.' . $id . '.on("blur", function() {  ' . $validate . ' });
                    CKEDITOR.instances.' . $id . '.on("keyup", function() {  ' . $validate . ' });

                    CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
                    CKEDITOR.config.forcePasteAsPlainText = false;
                    CKEDITOR.config.basicEntities = true;
                    CKEDITOR.config.entities = true;
                    CKEDITOR.config.entities_latin = false;
                    CKEDITOR.config.entities_greek = false;
                    CKEDITOR.config.entities_processNumerical = false;
                    CKEDITOR.config.fillEmptyBlocks = function (element) {
                        return true;
                    };
                    CKEDITOR.config.allowedContent = true;
                    ';
            }
            HTML::add_link('ckeditor/ckeditor', 'js');
            self::addOnLoadScript($js_init . $validate);
        } else {
            $html .= '<div id="status_' . $id . '" class="alert alert-light">' . nl2br($value) . '</div>';
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createTextareaBasic($id, $label, $value, $rows, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $html = '';
        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }
        if (!$is_view) {
            $html .= '<textarea class="form-control control-area" id="' . $id . '" name="' . $id . '" rows="' . $rows . '" maxlength="4000" onkeyup="' . $validate . '" onblur="' . $validate . '">' . $value . '</textarea>'
                    . '<div id="status_' . $id . '">'
                    . '<div id="error_' . $id . '">&nbsp;</div>'
                    . '</div>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<div id="status_' . $id . '" class="alert alert-light">' . nl2br($value) . '</div>';
        }
        return $html;
    }

    public static function createInputText($id, $label, $value, $space_column, $is_newline, $validate, $maxlength, $is_view = false, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if (!$is_view) {
            $html .= '<input class="form-control" type="text" id="' . $id . '" name="' . $id . '" maxlength="' . $maxlength . '" value="' . $value . '" onkeyup="' . $validate . '" onblur="' . $validate . '" />'
                    . '<div id="status_' . $id . '">'
                    . '<div id="error_' . $id . '">&nbsp;</div>'
                    . '</div>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<div id="status_' . $id . '" class="alert alert-light">' . $value . '</div>';
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createInputTextBasic($id, $value, $validate, $maxlength, $is_view = false, $custom_class = '', $custom_style = '') {
        $html = '';

        if (!$is_view) {
            $value = str_replace('"', ' & quot;
        ', $value);
            $html .= '<input ' . HTML::set_classes_and_styles(['form-control'], $custom_class, $custom_style) . ' type = "text" id = "' . $id . '" name = "' . $id . '" maxlength = "' . $maxlength . '" value = "' . $value . '" onkeyup = "' . $validate . '" onblur = "' . $validate . '" />'
                    . '<div id = "status_' . $id . '">'
                    . '</div>';
            self::addOnLoadScript($validate);
        } else {
            $html .= '<div id = "' . $id . '" ' . HTML::set_classes_and_styles(["alert alert-light"], $custom_class, $custom_style) . '>' . $value . '</div>';
        }

        return $html;
    }

    public static function createInputPassword($id, $label, $space_column, $is_newline, $validate, $maxlength, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        $html .= '<input class = "form-control" type = "password" id = "' . $id . '" name = "' . $id . '" maxlength = "' . $maxlength . '" value = "" onkeyup = "' . $validate . '" onblur = "' . $validate . '" />'
                . '<div id = "status_' . $id . '">'
                . '<div id = "error_' . $id . '">&nbsp;</div> '
                . '</div>'
                . '</div>';
        self::addOnLoadScript($validate);

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createSelect($id, $label, $options, $value, $space_column, $is_newline, $validate, $is_view = false, $trans_area = '', $custom_class = '', $custom_style = '', $optgroup_index = false) {
        $previous_optgroup = '';
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if ($is_view) {
            $html .= '<div id = "status_' . $id . '" class="alert alert-light">';

            foreach ($options as $option) {
                if ($option[0] . '' == $value . '') {
                    $html .= $option[1];
                    break;
                }
            }

            $html .= '</div>';
        } else {
            $html .= '<select class = "form-control" id = "' . $id . '" name = "' . $id . '" onblur = "' . $validate . '" onchange = "' . $validate . '">'
                    . '<option value = "">' . Language::find('select_one_option', ['validation']) . '</option>';

            foreach ($options as $option) {
                if ($optgroup_index && $previous_optgroup != $option[$optgroup_index]) {
                    if ($previous_optgroup != '') {
                        $html .= '</optgroup>';
                    }
                    $html .= '<optgroup label = "' . $option[$optgroup_index] . '">';
                    $previous_optgroup = $option[$optgroup_index];
                }
                if ($trans_area != '') {
                    $translation = Language::find($id . '_' . $option[0], $trans_area);
                    $html .= '<option value = "' . $option[0] . '" ' . ($option[0] . '' == $value . '' ? 'selected = "selected"' : '') . '>' . ($translation ? $translation : $option[1]) . '</option>';
                } else {
                    $html .= '<option value = "' . $option[0] . '" ' . ($option[0] . '' == $value . '' ? 'selected = "selected"' : '') . '>' . $option[1] . '</option>';
                }
            }

            if ($optgroup_index) {
                $html .= '</optgroup>';
            }

            $html .= '</select>'
                    . '<div id = "status_' . $id . '">'
                    . '<div id = "error_' . $id . '">&nbsp;</div>'
                    . '</div>';
            self::addOnLoadScript($validate);
        }

        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createSelectBasic($id, $options, $value, $validate, $is_view = false, $trans_area = '', $custom_class = '', $custom_style = '', $optgroup_index = false) {
        $previous_optgroup = '';

        if ($is_view) {
            $html = '<div id = "status_' . $id . '" class="alert alert-light">';
            foreach ($options as $option) {
                if ($option[0] === $value) {
                    $html .= $option[1];
                    break;
                }
            }
        } else {
            $html = '<div id = "status_' . $id . '">'
                    . '<select ' . HTML::set_classes_and_styles(['form-control'], $custom_class, $custom_style) . ' id = "' . $id . '" name = "' . $id . '" onblur = "' . $validate . '" onchange = "' . $validate . '">'
                    . '<option value = "">' . Language::find('select_one_option', ['validation']) . '</option>';

            foreach ($options as $option) {
                if ($optgroup_index && $previous_optgroup != $option[$optgroup_index]) {
                    if ($previous_optgroup != '') {
                        $html .= '</optgroup>';
                    }
                    $html .= '<optgroup label = "' . $option[$optgroup_index] . '">';
                    $previous_optgroup = $option[$optgroup_index];
                }
                if ($trans_area != '') {
                    $translation = Language::find($id . '_' . $option[0], $trans_area);
                    $html .= '<option value = "' . $option[0] . '" ' . ($option[0] . '' == $value . '' ? 'selected = "selected"' : '') . '>' . ($translation ? $translation : $option[1]) . '</option>';
                } else {
                    $html .= '<option value = "' . $option[0] . '" ' . ($option[0] . '' == $value . '' ? 'selected = "selected"' : '') . '>' . $option[1] . '</option>';
                }
            }

            if ($optgroup_index) {
                $html .= '</optgroup>';
            }
            $html .= '</select>';
            self::addOnLoadScript($validate);
        }

        $html .= '</div>';
        return $html;
    }
    

    public static function createDatePicker($id, $label, $value, $space_column, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $value = isset($value) ? (is_a($value, 'DateTime') ? Date::object_to_screen($value) : Date::default_to_screen($value)) : '';
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if (!$is_view) {
            $html .= '<div class = "input-group date" id = "ig_' . $id . '" data-target-input = "nearest">'
                    . '<div class = "input-group-prepend" data-target = "#ig_' . $id . '" data-toggle = "datetimepicker">'
                    . '<div class = "input-group-text">'
                    . '<span class = "fa fa-calendar"></span>'
                    . '</div>'
                    . '</div>'
                    . '<input id = "' . $id . '" name = "' . $id . '" class = "form-control datetimepicker-input" data-target = "#ig_' . $id . '" maxlength = "11" value = "' . $value . '" type = "text" onblur = "' . $validate . '" placeholder="dd-mmm-yyyy" />'
                    . '</div>';
        }

        if ($is_view) {
            $html .= '<div id = "status_' . $id . '" class="alert alert-light">'
                    . ($value != "01-JAN-1900" ? $value : '');
        } else {
            $html .= '<div id = "status_' . $id . '">'
                    . '<div id = "error_' . $id . '"></div>';

            self::addOnLoadScript('$("#ig_' . $id . '").datetimepicker( {
            format: \'DD-MMM-YYYY\', keepInvalid: true, useStrict: true }); ' . $validate);
        }

        $html .= '</div>'
                . '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createDateTimePicker($id, $label, $value, $space_column, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $value = isset($value) ? (is_a($value, 'DateTime') ? Date::object_to_screen($value) : Date::default_to_screen($value)) : '';
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
            //$html .= self::br();
        }

        if (!$is_view) {
            $html .= '<div class="input-group date" id="ig_' . $id . '" data-target-input="nearest">
                        <div class = "input-group-prepend" data-target = "#ig_' . $id . '" data-toggle = "datetimepicker">
                            <div class = "input-group-text">
                                <span class = "fa fa-calendar"></span>
                            </div>
                        </div>
                        <input id = "' . $id . '" name = "' . $id . '" type="text" class="form-control datetimepicker-input" data-target="#ig_' . $id . '" value = "' . $value . '" onblur = "' . $validate . '" placeholder="dd-mmm-yyyy hh:mm"/>
                    </div>';
        }

        if ($is_view) {
            $html .= ($value != "01-JAN-1900" ? $value : '');
        } else {
            $html .= '<div id = "status_' . $id . '">';
            $html .= '<div id = "error_' . $id . '"></div>';

            self::addOnLoadScript('$("#ig_' . $id . '").datetimepicker({ keepInvalid: true, useStrict: true, format:\'DD-MMM-YYYY HH:mm\' }); ' . $validate);
        }

        $html .= '</div>'
                . '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }

    public static function createDatePickerBasic($id, $value, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        if (isset($value)) {
            $value = is_a($value, 'DateTime') ? Date::object_to_screen($value) : Date::default_to_screen($value);
        } else {
            $value = '';
        }

        $html = '';

        if ($is_view) {
            $html .= '<div ' . HTML::set_classes_and_styles(['alert alert-light'], $custom_class, $custom_style) . '>'
                . ($value != "01-JAN-1900" ? $value : '')
                . '</div>';
        } else {
            $html .= '<div id="ig_' . $id . '" data-target-input="nearest" ' . HTML::set_classes_and_styles(['input-group', 'date'], $custom_class, $custom_style) . '>'
                . '<div class="input-group-prepend" data-target="#ig_' . $id . '" data-toggle="datetimepicker">'
                . '<div class="input-group-text">'
                . '<span class="fa fa-calendar"></span>'
                . '</div>'
                . '</div>'
                . '<input id="' . $id . '"  name="' . $id . '" class="form-control datetimepicker-input" data-target="#ig_' . $id . '" maxlength = "11" value="' . $value . '" type="text" onblur="' . $validate . '" placeholder="dd-mmm-yyyy" />'
                . '</div>'
                . '<div id = "status_' . $id . '"></div>';

            self::addOnLoadScript('$("#ig_' . $id . '").datetimepicker({ format: \'DD-MMM-YYYY\', keepInvalid: true, useStrict: true  }); ' . $validate);
        }

        return $html;
    }

    public static function createHidden($id, $value = '') {
        $html = '<input type="hidden" id="' . $id . '" name="' . $id . '" value=\'' . $value . '\'>';
        return $html;
    }

    public static function createPopup($id, $title, $text = '', $btn_text = 'ok', $btn_action = '', $close_text = 'close', $close_action = '') {
        if ($close_text == 'close') $close_text = Language::find('close');
        if ($btn_text == 'ok') $btn_text = Language::find('ok');
        $html = '<div class="modal fade" id="' . $id . '" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel' . $id . '" aria-hidden="true">'
            . '<div class="modal-dialog">'
            . '<div class="modal-content">'
            . '<div class="modal-header">'
            . '<h4 class="modal-title" id="gridSystemModalLabel' . $id . '">' . $title . '</h4>'
            . '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            . '</div>'
            . '<div class="modal-body">'
            . '<p id="' . $id . '_text" style="white-space:normal; margin:0;">' . $text . '</p>'
            . '</div>'
            . '<div class="modal-footer">'
            . '<button id="' . $id . '_btn_yes" type="button" class="btn btn-danger" style="min-width:80px;" onclick="' . $btn_action . '" >' . $btn_text . '</button>'
            . '<button id="' . $id . '_btn_no" type="button" class="btn btn-default" style="min-width:80px;" data-dismiss="modal" onclick="' . $close_action . '">' . $close_text . '</button>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
        return $html;
    }

    public static function createPopupAlert($id, $title, $text, $close_text = 'ok', $close_action = '', $close_button = true) {
        if ($close_text == 'ok') $close_text = Language::find('ok');
        $html = '<div class="modal fade" id="' . $id . '" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel' . $id . '" aria-hidden="true">'
            . '<div class="modal-dialog">'
            . '<div class="modal-content">'
            . '<div class="modal-header">'
            . '<h4 class="modal-title" id="gridSystemModalLabel' . $id . '">' . $title . '</h4>'
            . ($close_button ? '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '')
            . '</div>'
            . '<div class="modal-body">'
            . '<p id="' . $id . '_text" style="white-space:normal; margin:0;">' . $text . '</p>'
            . '</div>'
            . '<div class="modal-footer">'
            . '<button id="' . $id . '_btn_no" type="button" class="btn btn-danger" style="min-width:80px;" data-dismiss="modal" onclick="' . $close_action . '">' . $close_text . '</button>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
        return $html;
    }

    public static function createHTML5_Input($type, $name, $label, $value, $options_json = '{}') {
        $options = json_decode($options_json);
        //text, password, email, number, radio, checkbox, hidden, date, file

        $tags = [];
        $datalist = '';

        array_push($tags, $value && in_array($type, ['radio', 'checkbox']) ? 'checked' : '');

        array_push($tags, isset($options->disabled) ? 'disabled' : '');
        array_push($tags, isset($options->required) && in_array($type, ['text', 'password', 'email', 'number', 'radio', 'date']) ? 'required' : '');
        array_push($tags, isset($options->placeholder) && in_array($type, ['text', 'password', 'email', 'number']) ? 'placeholder="' . $options->placeholder . '"' : '');
        array_push($tags, isset($options->pattern) && in_array($type, ['text', 'password']) ? 'pattern="' . $options->pattern . '"' : '');
        array_push($tags, isset($options->min) && in_array($type, ['text', 'password']) ? 'minlength="' . $options->min . '"' : '');
        array_push($tags, isset($options->max) && in_array($type, ['text', 'password']) ? 'maxlength="' . $options->max . '"' : '');
        array_push($tags, isset($options->min) && in_array($type, ['number']) ? 'min="' . $options->min . '"' : '');
        array_push($tags, isset($options->max) && in_array($type, ['number']) ? 'max="' . $options->max . '"' : '');

        if (isset($options->list) && is_array($options->list) && in_array($type, ['text'])) {
            array_push($tags, 'list="l1"');
            $datalist = '<datalist id="l1">';
            foreach ($options->list as $item) {
                $datalist .= '<option>' . $item . '</option>';
            }
            $datalist .= '</datalist>';
        }

        switch ($type) {
            case 'checkbox':
            case 'radio':
                $html = '<div class="form-check">'
                        . '<input type="' . $type . '" name="' . $name . '" class="form-check-input" ' . join(' ', $tags) . '/>'
                        . '<label for="' . $name . '" class="form-check-label">' . $label . '</label>'
                        . '</div>';
                break;
            case 'hidden':
                $html = '<input type="' . $type . '" value="' . $value . '" />';
                break;
            default:
                $html = '<div class="form-group">'
                        . '<label for="' . $name . '" >' . $label . '</label>'
                        . '<input type="' . $type . '" name="' . $name . '" value="' . $value . '" class="form-control" ' . join(' ', $tags) . '/>'
                        . $datalist
                        . '</div>';
                break;
        }

        return $html;
    }

    public static function createHTML5_DropDown($name, $label, $values, $options_json = '{}') {
        $options = json_decode($options_json);

        $options_html = '<option disabled selected value>Select one option</option>';
        $tags = [];

        if (isset($values) && is_array($values)) {
            foreach ($values as $value) {
                if (is_array($value)) {
                    $options_html .= '<option value="' . $value[0] . '">' . $value[1] . '</option>';
                } else {
                    $options_html .= '<option value="' . $value . '">' . $value . '</option>';
                }
            }
        }

        array_push($tags, isset($options->required) ? 'required' : '');

        $html = '<div class="form-group">'
            . '<label for="' . $name . '">' . $label . '</label>'
            . '<select name="' . $name . '" class="form-control" ' . join(' ', $tags) . '>' . $options_html . '</select>'
            . '</div>';
        return $html;
    }

    // set "is_disabled" to true when slider is used in conditional questions/fields
    public static function createVasSlider($question_id, $field_name, $field_value, $is_view = false, $is_disabled = false, $radio_class = '', $radio_style = '', $table_style = 'width: 100%; border: 0;') {
        $html = '';
        $radio_input = '';
        $radio_html = '';
        $label_html = '';

        // link to external files required by slider
        HTML::add_link('vendor/jquery-ui.min-slider', 'css');
        HTML::add_link('vendor/jquery-ui.min-slider', 'js');
        HTML::add_link('crf/vas_slider', 'js');
        HTML::add_link('crf/vas_slider', 'css');

        // create JS onload call for slider initialization
        // "init_vas_slider" is defined in vas_slider.js
        $slider_div_id = 'vas-slider-' . $question_id;
        $vas_init_function = JS::call_func('init_vas_slider', [$slider_div_id, $field_name]);
        $vas_validate_function = JS::call_func('check_radio_slider', [$slider_div_id, $field_name]);
        $slider_status = 'enabled';
        HTML::$js .= JS::set_onload($vas_init_function);

        if ($is_view) {
            $slider_status = 'readonly';
            $vas_validate_function = '';
        } elseif ($is_disabled) {
            $slider_status = 'disabled';
        }

        // mobile slider
        $html .= '<div id="vas-slider-' . $question_id . '" class="vas-slider">';
        $html .= '<div id="vas-slider-handle-' . $question_id . '" class="ui-slider-handle vas-slider-handle" data-status="' . $slider_status . '" data-selection="' . $field_value . '" ></div>';
        $html .= '</div>';

        // classic radios
        $html .= '<div id="vas-radios-' . $question_id .'"'. HTML::set_classes_and_styles(['vas-radios'], $radio_class, $radio_style) . '>';
        $html .= '<table '. HTML::set_classes_and_styles([], '', $table_style) . '>';

        // table row begins
        $radio_html = '<tr>';
        $label_html = '<tr>';

        // "check_radio_slider" is defined in vas_slider.js
        for ($i = 0; $i < 21; $i++) {
            $radio_input = Form_input::createRadioBasic($field_name, '', $field_value, floatval($i / 2), $vas_validate_function, $is_view);
            $radio_html .= '<td style="text-align: center; height: 40px;">' . $radio_input . '</td>';
            $label_html .= '<td style="text-align: center">' . floatval($i / 2) . '</td>';
        }

        // table row ends
        $radio_html .= '</tr>';
        $label_html .= '</tr>';

        $html .= $radio_html . $label_html . '</table>';
        $html .= '</div>';

        return $html;
    }

    public static function createVasLabelTable($label_left, $label_right) {
        $html = '';
        $html .= '<table style="width: 100%; margin-bottom: 15px; border: 0;">';
        $html .= '<tr>
                    <td style="width: 50%; text-align: left; font-weight: normal">' . $label_left . '</td>
                    <td style="width: 50%; text-align: right; font-weight: normal">' . $label_right . '</td>
                  </tr>';
        $html .= '</table>';
        return $html;
    }

    public static function createVasSliderCombo($label_left, $label_right, $question_id, $field_name, $field_value, $is_view = false) {
        $html = '';
        $html .= self::createVasLabelTable($label_left, $label_right);
        $html .= self::createVasSlider($question_id, $field_name, $field_value, $is_view);
        return $html;
    }

    public static function createAutocompleter($ajax_type, $id, $label, $space_column, $is_newline, $maxlength, $minlength = 3, $is_mandatory = false, $pattern = '[A-Za-z0-9 ]+', $custom_class = '', $custom_style = '') {
        $input_id = "input_" . $id;
        $space_column_classes = self::get_space_column_classes($space_column);
        $ajax_auto_url = Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . 'common/' . URL::$area_url . '/ajax_auto/' . $ajax_type . '/';
        if (!Strings::contains(HTML::$js, 'AUTO_NOT_FOUND')) {
            HTML::add_link('vendor/jquery-ui.min-autocomplete', 'css');
            HTML::add_link('autocompleter', 'css');
            HTML::add_link('vendor/jquery-ui.min-autocomplete', 'js');
            HTML::add_link('vendor/jquery.mark.min', 'js');
            HTML::add_link('autocompleter', 'js');
            HTML::$js .= " var AUTO_NOT_FOUND = '" . Ajax_autocompleter::NOT_FOUND . "'; ";
            HTML::$js .= " var AUTO_ERROR = 'Incorrect server response, please try again.'; ";
            HTML::$js .= " var AUTO_INCORRECT = 'Incorrect value entered'; ";
            HTML::$js .= " var AUTO_NO_SELECTED = 'An option should be selected'; ";
            HTML::$js .= " var AUTO_PATTERN = /" . $pattern . "/; ";
        }
        HTML::$js .= JS::set_onload("init_autocompleter('" . $input_id . "', '" . $id . "', '" . $ajax_auto_url . "', " . ( $is_mandatory ? 'true' : 'false' ) . ", " . $minlength . ");");
        $html = '';
        $html .= self::createHidden($id);
        $html .= '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }
        $method = '';
        $validate = $is_mandatory ? "check_auto_error('" . $input_id . "', '" . $id . "');" : "";
        $html .= '<input class="form-control" type="text" id="' . $input_id . '" name="' . $input_id . '" maxlength="' . $maxlength . '" onkeyup="' . $validate . '" onblur="' . $validate . '" value="" />'
                . '<div id="status_' . $input_id . '">'
                . '<div id="error_' . $input_id . '">&nbsp;</div>'
                . '</div>';
        self::addOnLoadScript($validate);
        $html .= '</div>';

        if ($is_newline) {
            $html .= self::br();
        }

        return $html;
    }


    public static function createMultiSelect($id, $label, $options, $values, $space_column, $is_newline, $validate, $is_view = false, $custom_class = '', $custom_style = '') {
        $space_column_classes = self::get_space_column_classes($space_column);
        $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';

        if (!empty($label)) {
            $html .= HTML::set_label($id, $label, true);
        }

        if ($is_view) {
            $html .= '<div id = "status_' . $id . '" class="alert alert-light">';
            foreach ($options as $option) {
                if (in_array($option[0], $values)) {
                    $html .= $option[1];
                    break;
                }
            }
        } else {
            $html .= '<div id = "status_' . $id . '"><select id = "' . $id . '" name = "' . $id . '[]" onblur = "' . $validate . '" onchange = "' . $validate . '" multiple data-actions-box="true" data-width="100%" data-size="10">';
            foreach ($options as $option) {
                $html .= '<option value="' . $option[0] . '" title="' . $option[1] . '" ' . (in_array($option[0], $values) ? 'selected' : '') . ' ' . (isset($option[2]) ? 'style="background: #' . $option[2] . ';"' : '') . '>' . $option[1] . '</option>';
            }
            $html .= '</select><div id = "error_' . $id . '">&nbsp;</div>';
        }
        $html .= '</div></div>';
        self::addOnLoadScript(JS::call_func('initializeMultiSelect', [$id]) . ' ' . $validate);
        if ($is_newline) {
            $html .= self::br();
        }
        return $html;
    }

    public static function get_space_column_classes($space_column) {
        $space_classes = '';
        // when non-array value provided as space_column, return only LG and MD classes
        if (!is_array($space_column)) {
            $md_space_column = 12;
            $lg_space_column = $space_column;
            $space_classes = self::LG_SPACE_PREFIX . $lg_space_column . ' ' . self::MD_SPACE_PREFIX . $md_space_column;
        }
        // when array passed, return all elements which were defined, i.e. non-null
        else {
            $space_classes .= isset($space_column[0]) ? self::LG_SPACE_PREFIX . $space_column[0] . ' ' : '';
            $space_classes .= isset($space_column[1]) ? self::MD_SPACE_PREFIX . $space_column[1] . ' ' : '';
            $space_classes .= isset($space_column[2]) ? self::SM_SPACE_PREFIX . $space_column[2] . ' ' : '';
            $space_classes .= isset($space_column[3]) ? self::XS_SPACE_PREFIX . $space_column[3] . ' ' : '';
        }
        return $space_classes;
    }

    public static function createFileInput($id, $space_column, $is_newline, $is_view = false, $required = false, $custom_class = '', $custom_style = '') {
        $html = '';
        //dsffgsd
        if (!$is_view) {
            if (!Strings::contains(HTML::$js, 'EXT_ALLOWED')) {
                HTML::$js .= " var EXT_ALLOWED = " . json_encode(File::$extensions_allowed) . "; ";
                HTML::$js .= " var EXT_ERROR = '" . File::get_error(File::ERROR_EXTENSION) . "'; ";
                HTML::$js .= " var SIZE_MAX = " . File::$size_bytes_max . "; ";
                HTML::$js .= " var SIZE_ERROR = '" . File::get_error(File::ERROR_SIZE) . "'; ";
            }

            $space_column_classes = self::get_space_column_classes($space_column);
            $validate = 'check_file(\'' . $id . '\', ' . ($required ? "true" : "false") . ');';
            $html = '<div ' . HTML::set_classes_and_styles(['form-group', $space_column_classes], $custom_class, $custom_style) . '>';
            $html .= '<input type="file" id="' . $id . '" name="' . $id . '" onchange="' . $validate . '" />';
            $html .= HTML::set_button(Icon::set_clear() . 'Clear', ' $(\'#' . $id . '\').val(\'\'); ' . $validate);
            $html .= '<br>';
            $html .= '<div id = "status_' . $id . '">';
            $html .= '<div id = "error_' . $id . '">&nbsp;</div>';
            $html .= '</div>';
            $html .= '</div>';
            self::addOnLoadScript($validate);
        }
        return $html;
    }

    public static function createFileFlow($ajax_type, $flow_id, $folder, $upload_on_assign, $max_size, $extensions, $drop_width = 100, $accept = '', $is_view = false) {
        HTML::add_link('vendor/flow', 'js');
        HTML::add_link('flowfile', 'js');
        URL::changeable_var_add('fn', $ajax_type);
        URL::changeable_var_add('fta', File_flow::ACTION_UPLOAD);
        URL::changeable_var_add('nm', $flow_id);
        URL::changeable_var_add('fd', $folder);
        URL::changeable_var_add('max', $max_size);
        URL::changeable_var_add('exts', json_encode($extensions));
        URL::changeable_var_add('x', rand(0, 9999));
        $html = '';
        if (!$is_view) {
            HTML::$js .= ' var ' . $flow_id . ' = new Flow({
                    target: "' . URL::create_url('ajax') . '", singleFile: true, testChunks: false, allowDuplicateUploads: true, 
                    query: {"1" : "1"' . Security::set_token(Security::TYPE_POST_JSON) . '}
                });
                create_flow("' . $flow_id . '", "' . $accept . '", ' . $upload_on_assign . ');
                ';
            $html .= '<div id="' . $flow_id . '_msg"></div>';
            $html .= Html::set_button(Icon::set_upload() . ($upload_on_assign ? "Upload" : "Choose file"), '', '', $flow_id . '_file', '', '', 'bcee68');
            if ($drop_width > 0) {
                $html .= Form_input::br();
                $html .= '<div id="' . $flow_id . '_drop" style="text-align: center; postion: relative; width: ' . $drop_width . ($drop_width == 100 ? '%' : 'px') . '; height: 50px; border: 1px dotted #666; margin: 5px; cursor: copy;">';
                $html .= 'Drag file here';
                $html .= '</div>';
            }
            $html .= '<div id="' . $flow_id . '_prog" class="progress">
                <div id="' . $flow_id . '_prog_bar" class="progress-bar"><span id="' . $flow_id . '_prog_txt" style="font-size: 15px"></div>
            </div>';
        } else if ($upload_on_assign) {
            
        }
        return $html;
    }

}
