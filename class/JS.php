<?php

class JS {

    public static function set_login_redirect() {
        $body = '';
        URL::changeable_vars_reset();
        $body .= 'window.location.replace("' . URL::create_url('login') . '");';
        HTML::$js .= self::set_func('login_redirect', $body);
    }

    public static function set_ajax($url, $type = '', $method = '', $data = '', $done = '', $always = '', $fail = '') {
        $js = '$.ajax({ '
                . 'url: "' . $url . '/" + Math.floor((Math.random() * 10000) + 1), '
                . (empty($method) ? '' : 'method: "' . $method . '", ')
                . (empty($data) ? '' : 'data: { ' . $data . Security::set_token(Security::TYPE_POST_JSON) . ' }, ')
                . (empty($type) ? '' : 'dataType: "' . $type . '", ')
                . 'cache: false '
                . '}) '
                . (empty($done) ? '' : '.done(function(data) { ' . $done . ' }) ')
                . (empty($fail) ? '' : '.fail(function(xhr, status, error) { ' . $fail . ' }) ')
                . (empty($always) ? '' : '.always(function(xhr, status) { ' . $always . ' }) ')
                . '; ';
        return $js;
    }

    public static function set_func($function, $body, $params = '') {
        $js = 'function ' . $function . '(' . $params . ') { '
                . $body
                . '} ';
        return $js;
    }

    public static function add_external_links($js_ext_links_array) {
        $js_links = '';
        foreach ($js_ext_links_array as $url) {
            $js_links .= '<script src="' . $url . '"></script>';
        }
        return $js_links;
    }

    public static function add_js_links($js_links_array, $domain, $url) {
        $js_links = '';
        foreach ($js_links_array as $name) {
            $rtl_folder = Language::$is_right_dir && in_array($name, ['bootstrap.min']) ? 'rtl/' : '';
            $folder = substr($name, 0, strpos($name, '/'));
            if (in_array($folder, ['ckeditor', 'rtl', 'vendor']) || $_SERVER["SERVER_NAME"] == 'localhost') { // if localhost no parameter is added to ease debugging 
                $js_links .= '<script src="' . $domain . $url . 'js/' . $rtl_folder . $name . '.js"></script>';
            } else {
                $js_links .= '<script src="' . $domain . $url . 'js/' . $rtl_folder . $name . '.js?' . rand(0, 666) . '"></script>';
            }
        }
        return $js_links;
    }

    public static function set_javascript($javascript, $on_load, $page_array) {
        $output = '<script>'
                . 'if (top.frames.length != 0) top.location = self.document.location; '
                . 'function mobile_click(id) { '
                . 'if ($(".navbar-toggle").is(\':visible\')) { '
                . 'if (typeof(id) == \'undefined\' || id == null || id == \'[object UIEvent]\') '
                . 'id = "' . $page_array[0] . '"; '
                . 'if ($("#menu_" + id + ".open").length > 0) '
                . '$(".dropdown").show("slow"); '
                . 'else '
                . '$(".dropdown").hide("slow"); '
                . '$("#menu_" + id).show("slow"); '
                . '} else { '
                . '$(".dropdown").show(); '
                . '} '
                . '} '
                . 'function mobile_check() { '
                . 'if ($(".navbar-toggle").is(\':hidden\')) '
                . '$(".dropdown").show(); '
                . '} '
                . 'window.onresize = mobile_check; ';

        if (!empty($javascript)) {
            $output .= $javascript;
        }

        $output .= self::set_onload($on_load)
                . '</script>';

        $output = html_entity_decode($output);
        $output = preg_replace('!\s+!', ' ', $output);
        $remove = array("\n", "\r\n", "\r", "\t");
        return str_replace($remove, ' ', $output);
    }

    public static function set_onload($javascript) {
        return '$(document).ready(function() { '
                . $javascript
                . '}); ';
    }

    public static function add_newline($multiplier = 1) {
        return str_repeat("\n", $multiplier);
    }

    public static function add_tab($multiplier = 1) {
        return str_repeat("\t", $multiplier);
    }

    public static function call_func($function_name, $args = NULL) {
        if (is_null($args)) {
            return $function_name . "();";
        }
        return $function_name . "('" . join("', '", $args) . "');";
    }

    public static function create_specify_check_call($controller, $controlled, $controller_value = null, $to_disable = null) {
        return self::create_validate_and_specify_check_call($controller, false, $controlled, $controller_value, $to_disable);
    }

    public static function create_validate_and_specify_check_call($controller, $controller_must_be_validate, $controlled, $controller_value = null, $to_disable = null) {
        if (!is_array($controlled)) {
            throw new InvalidArgumentException();
        }
        if (is_array($controller)) {
            $controller = htmlentities(json_encode($controller));
        }

        $args = [$controller, htmlentities(json_encode($controlled))];
        if (!is_null($controller_value)) {
            if (is_array($controller_value)) {
                $args[] = htmlentities(json_encode($controller_value));
            } else {
                $args[] = htmlentities(json_encode([$controller_value]));
            }
        }
        if ($to_disable) {
            $args[] = htmlentities(json_encode($to_disable));
        }

        if ($controller_must_be_validate) {
            return self::call_func('validate_and_specify_check', $args);
        } else {
            return self::call_func('specify_check', $args);
        }
    }

    public static function create_post_button_click_call($args) {
        return self::call_func('post_button_click', [htmlentities(json_encode($args))]);
    }

    public static function set_responsive_lang() {
        return ' language: {
            "emptyTable": "' . Language::find('no_row_found') . '",
            "infoEmpty": "",
            "search": "' . Language::find('search') . '",
            "lengthMenu": "' . Language::find('DT_lengthMenu') . '",
            "info": "' . Language::find('DT_info') . '",
            paginate: {
                next: "' . Language::find('next') . '",
                previous: "' . Language::find('previous') . '"
            }
        } ';
    }
}
