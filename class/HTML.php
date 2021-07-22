<?php

class HTML {

    //----------------------------------------------VARIABLES----------------------------------------------
    public static $page_array = array();
    public static $title = Config::TITLE;
    private static $audit_trail = '';
    private static $audit_search = '';
    private static $visit_block = '';
    public static $description = '';
    private static $js_links = array();
    private static $js_ext_links = array();
    public static $js = '';
    public static $js_onload = "if(!!window.performance && window.performance.navigation.type == 2) { window.location.reload(); } ";
    private static $css_links = array();
    public static $css = '';
    public static $logo_name = 'logo';
    public static $debug_info = '';

    const BR = '<br style="clear: both" />';
    const HR = '<hr />';
    const TR_DEFAULT = 0;
    const TR_HEAD = 1;
    const TR_BODY = 2;

    //----------------------------------------------CONSTRUCTOR----------------------------------------------
    public static function set_default_css_js() {
        global $oArea;
        self::add_link("vendor/animation", "css");
        self::add_link("vendor/fontawesome-all.min", "css");
        self::add_link("vendor/v4-shims.min", "css");
        self::add_link("vendor/bootstrap.min", "css");
        self::add_link("checkbox", "css");
        self::add_link("radio", "css");
        self::add_link("site", "css");
        if ($oArea->id != 0) {
            self::add_link("area_" .strtolower($oArea->url), "css");
        }

        self::add_link("vendor/jquery-3.6.0.min", "js");
        self::add_link("vendor/popper.min", "js");
        self::add_link("vendor/bootstrap.min", "js");
        self::add_link("vendor/moment", "js");
        self::add_link("vendor/es6-shim.min", "js");
        self::add_link("utils", "js");
        self::$logo_name = 'logo' . Config::SITEVERSION;
    }

    //----------------------------------------------PUBLIC FUNCTIONS----------------------------------------------
    public static function set_table_responsive($content, $id_table, $javascript = '', $class = '', $style = '', $other = '') {
        self::add_link('vendor/datatables.min', 'css');
        self::add_link('vendor/responsive.datatables.min', 'css');
        self::add_link('responsive.datatables-site', 'css');
        self::add_link('vendor/datatables.min', 'js');
        self::add_link('vendor/datatables.responsive.min', 'js');
        self::add_link('vendor/date-dd-MMM-yyyy', 'js');
        self::$js_onload .= '
                $("#' . $id_table . '").DataTable( {
                "order": [] ' . ($javascript != '' ? ',' . $javascript : '') . ',
                "iDisplayLength": 50,
                "searchHighlight": true,
                "responsive": true,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]] } ); ';
        $class = 'display responsive nowrap' . $class;
        $style = ' style="width:100%; ' . (Language::$is_right_dir ? 'direction: rtl' : '') . ' ' . $style . '" ';
        return self::set_table($content, $id_table, $style . $other, $class);
    }

    public static function set_table($content, $id_table = '', $other = '', $class = 'table table-hover') {
        return '<table ' . ($id_table != '' ? 'id="' . $id_table . '"' : '') . ' ' . ($class != '' ? 'class="' . $class . '"' : '') . ' ' . $other . '>' . $content . '</table>';
    }

    public static function set_tbody($content, $class = '') {
        return '<tbody class="' . $class . '">' . $content . '</tbody>';
    }

    public static function set_tr($content, $is_thead = false, $class = '') {
        $tr = '<tr class="' . $class . '">' . $content . '</tr>';
        if ($is_thead) {
            $tr = '<thead>' . $tr . '</thead>';
        }
        return $tr;
    }

    public static function set_td($content, $colspan = '', $is_th = false, $class = '', $style = '') {
        return '<t' . ($is_th ? 'h' : 'd') . ' ' . ($colspan != '' ? 'colspan="' . $colspan . '"' : '') . ' class="' . $class . '" style="' . $style . '">' . $content . '</t' . ($is_th ? 'h' : 'd') . '>';
    }

    //BOOTSTRAP FUNCTION

    public static function set_row($content, $background = '', $style = '') {
        $output = '<div class="row ' . $background . '" style="' . $style . '">' . $content . '</div>';
        return $output;
    }

    public static function set_bootstrap_cell($content, $space = 2, $bold = false, $border = '', $style = '') {
        return '<div class="col-md-' . $space . '  ' . $border . '" style="' . $style . '">' .
            ($bold == true ? '<strong>' : '') . $content . ($bold == true ? '</strong>' : '') .
        '</div>';
    }

    //used in patient index in order to take together header and content
    public static function set_bootstrap_nested_cell($label, $content, $space = 2, $bold = false, $border = '', $style = '') {
        return '<div class="col-md-' . $space . '  ' . $border . '"  style="' . $style . '">' .
            ($bold == true ? '<strong>' : '') . $label . ($bold == true ? '</strong>' : '') . $content .
        '</div>';
    }

    public static function add_link($name, $type) {
        $name = str_replace(' ', '_', $name);
        if ($type == "css") {
            if (!in_array($name, self::$css_links)) {
                array_push(self::$css_links, $name);
            }
        } else if ($type == "js") {
            if (!in_array($name, self::$js_links)) {
                array_push(self::$js_links, $name);
            }
        } else if ($type == "extjs") {
            if (!in_array($name, self::$js_ext_links)) {
                array_push(self::$js_ext_links, $name);
            }
        }
    }

    public static function remove_link($name, $type) {
        $name = str_replace(' ', '_', $name);
        if ($type == "css") {
            self::$css_links = array_diff(self::$css_links, array($name));
        } else if ($type == "js") {
            self::$js_links = array_diff(self::$js_links, array($name));
        }
    }

    //THIS FUNCTION ALREADY CREATES A ROW (OPEN & CLOSED) INSIDE THE FORM AND IT PUTS YOUR HTML INSIDE THAT ROW
    static function set_form($html, $id = 'form1', $other = '', $method = 'post', $action = '', $add_row = true, $is_upload = false) {
        if (!in_array("validation", self::$js_links)) {
            Language::add_area("validation");
            if (isset(Language::$area_translations["validation"])) {
                foreach (Language::$area_translations["validation"] as $translation) {
                    self::$js .= ' const ' . strtoupper($translation['label_text']) . ' = "' . Language::get_sanitized_value($translation) . '"; ';
                }
            }
        }
        self::add_link("vendor/tempusdominus-bootstrap-4.min", "css");
        self::add_link("vendor/bootstrap-select.min", "css");
        self::add_link("vendor/ajax-bootstrap-select.min", "css");
        self::add_link("vendor/tempusdominus-bootstrap-4.min", "js");
        self::add_link("vendor/bootstrap-select.min", "js");
        self::add_link("vendor/ajax-bootstrap-select.min", "js");
        self::add_link("validation", "js");

        $form = '';
        $form .= '<form id="' . $id . '" method="' . $method . '" ' . ($action == '' ? '' : 'action="' . $action . '"') . ' ' . ($is_upload ? 'enctype="multipart/form-data"' : '') . ' ' . $other . ' >';
        if ($add_row) {
            $form .= self::set_row($html);
        } else {
            $form .= $html;
        }
        $form .= Security::set_token();
        $form .= '</form>';
        return $form;
    }

    static function set_button($value, $click = '', $href = '', $id = '', $style = '', $color_font = '', $color_background = '', $other = '') {
        $button = '';
        $button .= '<a class="btn btn-primary" style="' . $style . ' ' . ($color_font . '' == '' ? '' : 'color: #' . $color_font . ';') . ' ' . ($color_background . '' == '' ? '' : 'background-color: #' . $color_background . ';') . '" ';
        $button .= '' . ($href == '' ? '' : 'href="' . $href . '"') . ' ' . ($click == '' ? '' : 'onclick="' . $click . '"') . ' ' . ($id == '' ? '' : 'id="' . $id . '"') . ' ' . $other . '>';
        $button .= $value;
        $button .= '</a>';
        return $button;
    }

    public static function set_button_with_tooltip($value, $tooltip_message, $click = '', $href = '', $id = '', $style = '', $color_font = '', $color_background = '', $other = '') {
        if (!Strings::contains(HTML::$js_onload, '$(function () {$(\'[data-toggle="tooltip"]\').tooltip({trigger: "hover"})});')) {
            HTML::$js_onload .= '$(function () {$(\'[data-toggle="tooltip"]\').tooltip({trigger: "hover"})});';
        }
        $other .= ' data-toggle="tooltip" data-placement="top" title="' . $tooltip_message . '"';
        return self::set_button($value, $click, $href, $id, $style, $color_font, $color_background, $other);
    }

    static function set_button_denied($value, $id = '', $style = '', $color_font = '', $color_background = '', $other = '') {
        $button = '';
        $button .= '<a class="btn btn-primary" style="cursor: default;' . $style . ' ' . ($color_font . '' == '' ? '' : 'color: #' . $color_font . ';') . ' ' . ($color_background . '' == '' ? '' : 'background-color: #' . $color_background . ';') . '" ';
        $button .= ' ' . ($id == '' ? '' : 'id="' . $id . '"') . ' ' . $other . '>';
        $button .= Icon::set_access_denied() . ' ' . $value;
        $button .= '</a>';
        return $button;
    }

    static function set_detail_block($oPatient, $oVisit = NULL) {
        global $oUser, $oArea;
        self::$visit_block = '';
        $is_anonymous = in_array($oArea->id, [Area::$ID_ADMIN]);

        if (isset($oPatient) && $oPatient->id != 0) {

            $style_row = 'background-color: #' . $oArea->color_background . '; color: #' . $oArea->color_font . '; margin: 0; ';
            $style_cell = 'text-align: left; padding: 5px; ';

            self::$visit_block .= '<div class="row" style="' . $style_row . '">';
            if (in_array($oArea->id, [Area::$ID_ADMIN, Area::$ID_INVESTIGATOR])) {
                self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
                self::$visit_block .= Language::find('patient_code').': <b style="white-space: nowrap;">' . $oPatient->patient_id . '</b>';
                self::$visit_block .= '</div>';
            }
            if (in_array($oArea->id, [Area::$ID_INVESTIGATOR])) {
                self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
                self::$visit_block .= Language::find('fullname').': <b style="white-space: nowrap;">' . ($oPatient->first_name == 'Encrypted' ? 'Encrypted' : $oPatient->first_name . ' ' . $oPatient->last_name) . '</b>';
                self::$visit_block .= '</div>';
            } else {
                self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
                self::$visit_block .= Language::find('export_code').': <b style="white-space: nowrap;">' . $oPatient->export_id . '</b>';
                self::$visit_block .= '</div>';
            }
            //self::$visit_block .= '</div>';

            //self::$visit_block .= '<div class="row" style="' . $style_row . '">';
            self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
            self::$visit_block .= Language::find('sex').': <b style="white-space: nowrap;">' . $oPatient->get_gender_text() . '</b>';
            self::$visit_block .= '</div>';
            self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
            self::$visit_block .= Language::find('diagnosis').': <b style="white-space: nowrap;">' . $oPatient->dia_name . '</b>';
            self::$visit_block .= '</div>';
            self::$visit_block .= '</div>';
        }
        if (isset($oVisit) && $oVisit->id != 0) {
            self::$visit_block .= '<div class="row" style="' . $style_row . '">';
            self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
            self::$visit_block .= Language::find('visit').': <b style="white-space: nowrap;">' . $oVisit->type_text . '</b>';
            self::$visit_block .= '</div>';
            self::$visit_block .= '<div class="col-sm-3" style="' . $style_cell . '">';
            self::$visit_block .= Language::find('date').': <b style="white-space: nowrap;">' . Date::default_to_screen($oVisit->date) . '</b>';
            self::$visit_block .= '</div>';
            self::$visit_block .= '</div>';
        }
        self::$visit_block .= HTML::BR;
    }

    static function set_audit_trail(User $oAuthor, $ludati) {
        if ($ludati.'' != '') {
            $text = Language::find('last_update');
            $text = str_replace('%%%', $oAuthor->name . ' ' . $oAuthor->surname, $text);
            $text = str_replace('$$$', Date::default_to_screen($ludati, true), $text);
            self::$audit_trail = $text;
        }
    }

    static function set_audit_archive($pk_array = []) {
        URL::changeable_vars_from_onload_vars();
        URL::changeable_var_add('pk_array', json_encode($pk_array));
        self::$audit_search = self::set_button(Icon::set_archive() . 'Audit Trail', '', URL::create_url('archive'));
        URL::changeable_var_remove('prj_class');
        URL::changeable_var_remove('pk_array');
    }

    static function set_br($occurences = 1) {
        $html = '';
        for ($o = 1; $o <= $occurences; $o++) {
            $html .= self::BR;
        }
        return $html;
    }

    public static function set_spaces($occurences = 1) {
        $html = '';
        for ($o = 1; $o <= $occurences; $o++) {
            $html .= '&nbsp;';
        }
        return $html;
    }

    public static function set_paragraph($title, $other_style = '') {
        $html = '';
        $html .= self::BR;
        $html .= self::HR;
        $html .= '<div class="testorosso" style="font-weight: bold; font-size: 16px; width: 100%; margin: -15px 0px -40px 0px; padding: 5px; ' . $other_style . '">';
        $html .= strtoupper($title);
        $html .= '</div>';
        $html .= self::BR;
        $html .= self::BR;
        return $html;
    }

    public static function set_label($id, $label, $is_strong = false) {
        $text = html_entity_decode($label);
        if ($is_strong) {
            $text = '<strong>' . (Strings::contains($text, '(') && Strings::endsWith($text, ')') ? str_replace('(', '</strong>(', $text) : $text . '</strong>');
            //$text = '<strong>' . $text . '</strong>';
        }
        return '<label ' . (empty($id) ? '' : 'id="label_' . $id . '"') . ' class="control-label">' . $text . '</label>';
    }

    public static function set_text($text, $is_bold = false, $style = '', $class = '') {
        $classes_and_styles = self::set_classes_and_styles([], $class, $style);
        if ($is_bold) {
            return '<b ' . $classes_and_styles . '>' . $text . '</b>';
        }
        return '<span ' . $classes_and_styles . '>' . $text . '</span>';
    }

    public static function set_classes_and_styles($classes, $custom_class = '', $custom_style = '') {
        return 'class="' . join(' ', $classes) . (empty($custom_class) ? '' : ' ' . $custom_class) . '"' . (empty($custom_style) ? '' : ' style="' . $custom_style . '"');
    }

    public static function set_warning_message($message, $add_row = true) {
        $warning_box = HTML::set_bootstrap_cell(HTML::set_text('<i class="' . Globals::ICON_WARNING . '"></i> ' . $message, true), 12, false, 'alert alert-warning');
        return ($add_row ? HTML::set_row($warning_box) : $warning_box);
    }

    //----------------------------------------------PRINT HTML----------------------------------------------
    public static function print_html($html) {
        self::$page_array = [];
        if (Globals::$URL_RELATIVE != '/') {
            self::$page_array = explode("/", str_replace(Globals::$URL_RELATIVE, "", Security::sanitize(INPUT_SERVER, "REQUEST_URI")));
        } else {
            self::$page_array = explode("/", Security::sanitize(INPUT_SERVER, "REQUEST_URI"));
        }
        header('X-Frame-Options: DENY'); // displaying in a frame never allowed
        echo '<!DOCTYPE html><html lang="en" ' . (Language::$is_right_dir ? 'dir="rtl"' : '') . '><head>';
        echo '<meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge">';
        echo '<meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Cache-Control" content="no-cache,no-Store">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>' . str_replace(self::BR, ' ', str_replace('<br />', ' ', str_replace('</br>', ' ', str_replace('<br>', ' ', self::$title)))) . '</title>';

        //----------------------------------------------CSS LINKS----------------------------------------------
        foreach (self::$css_links as $name) {
            $rtl_folder = Language::$is_right_dir && in_array($name, ['bootstrap.min']) ? 'rtl/' : '';
            $folder = substr($name, 0, strpos($name, '/'));
            if (in_array($folder, ['ckeditor', 'rtl', 'vendor'])) {
                echo '<link href="' . Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . 'css/' . $rtl_folder . $name . '.css" rel="stylesheet">';
            } else {
                echo '<link href="' . Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . 'css/' . $rtl_folder . $name . '.css?' . rand(0, 666) . '" rel="stylesheet">';
            }
        }

        //----------------------------------------------EXTRA CSS & JS----------------------------------------------
        echo '<style>' . self::$css . '</style>';
        echo '</head><body>';

        //----------------------------------------------MENU & HTML----------------------------------------------
        global $oMenu;
        $has_menu = $oMenu->has();
        echo '<nav class="navbar navbar-expand-lg navbar-default navbar-light fixed-top">';
        if (!$has_menu) {
            echo '<div class="centered-element">';
        }
        if ($has_menu) {
            echo '<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar" onclick="$(\'.dropdown\').show();">';
            echo '<span class="navbar-toggler-icon"></span>';
            echo '</button>';
        }
        $logo_url = '';
        if (!isset($_SESSION[URL::$prefix . 'user'])) {
            URL::changeable_vars_reset();
            $logo_url = URL::create_url('login');
        } else {
            URL::changeable_vars_reset();
            global $oArea;
            $logo_url = URL::create_url($oArea->default_page);
            URL::changeable_vars_from_onload_vars();
        }
        echo '<a class="navbar-brand" href="' . $logo_url . '">';
        echo '<div class="logo-fixed">';
        echo '<span style="font-size: 25px; margin-left: 20px; font-weight: bold;">' . Config::TITLE . (Config::SITEVERSION != '' ? ' '.Config::SITEVERSION : '') . '</span>';
        echo '</div>';
        echo '</a>';
        if (!$has_menu) {
            echo '</div>';
        }
        if ($has_menu) {
            $oMenu->draw();
        }
        echo '</nav>';

        //----------------------------------------------MENU & HTML----------------------------------------------
        echo '<div class="container-fluid"><div class="jumbotron">';
        if (Config::SITEVERSION == 'TEST') {
            $onload_vars = URL::DEBUG_onload_string();
            $no_debug_vars = defined('Config::NO_DEBUG_VARS');
            if (!empty($onload_vars) && !$no_debug_vars) {
                echo 'ONLOAD VARS: ' . $onload_vars . HTML::set_br(1);
            }
            echo HTML::$debug_info;
        }
        echo '<div class="centered-element">';
        if (self::$visit_block != '') {
            echo self::$visit_block;
        }
        if (self::$title != '') {
            echo '<h2 id="title_page">';
            echo self::$title;
            echo '</h2>';
        }
        if (self::$audit_trail != '') {
            echo '<h6 style="margin-top: -25px;">';
            echo self::$audit_trail;
            echo '</h6>';
        }
        if (self::$audit_search != '') {
            echo '<div style="margin-top: -25px; margin-bottom: 30px;">';
            echo self::$audit_search;
            echo '</div>';
        }
        echo '</div>';
        echo $html;
        echo '<br>';

        //----------------------------------------------ERROR MESSAGE----------------------------------------------
        echo '<div class="centered-element">';
        echo '<div id="table_error" style="display: none;">';
        echo '<table class="message_text error_text"><tr><td style="vertical-align: middle; padding: 0px 8px; " >';
        echo '<i class="' . Globals::ICON_ERROR . '" ></i>';
        echo '</td><td style="font-weight: bold; vertical-align: middle;" id="table_text_error">';
        echo '</td></tr></table>';
        echo HTML::BR;
        echo '</div>';
        echo '<div id="table_warning" style="display: none;">';
        echo '<table class="message_text warning_text"><tr><td style="vertical-align: middle; padding: 0px 8px; " >';
        echo '<i class="' . Globals::ICON_WARNING . '" ></i>';
        echo '</td><td style="font-weight: bold; vertical-align: middle;" id="table_text_warning">';
        echo '</td></tr></table>';
        echo HTML::BR;
        echo '</div>';
        echo '<div id="table_ok" style="display: none;">';
        echo '<table class="message_text ok_text"><tr><td style="vertical-align: middle; padding: 0px 8px; " >';
        echo '<i class="' . Globals::ICON_CHECKED . '" ></i>';
        echo '</td><td style="font-weight: bold; vertical-align: middle;" id="table_text_ok">';
        echo '</td></tr></table>';
        echo '</div>';
        echo '</div>';

        //----------------------------------------------FOOTER----------------------------------------------
        echo '</div><div id="footer"><div class="centered-element col-lg-12">';
        echo Config::FOOTER;
        echo '</div></div>';
        echo '</div>';
        //javascript
        Message::write();
        echo JS::add_external_links(self::$js_ext_links) . JS::add_js_links(self::$js_links, Globals::$DOMAIN_URL, Globals::$URL_RELATIVE)
        . JS::set_javascript(self::$js, self::$js_onload, self::$page_array);

        echo '</body></html>';
    }

}
