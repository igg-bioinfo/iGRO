<?php

class Icon {

    public static function set($icon, $size = 2, $class = '', $style = '') {
        return '<i class="fa fa-' . $icon . ' fa-' . $size . 'x ' . $class . '" ' . ($style == '' ? '' : 'style="' . $style . '"') . '></i>';
    }

    public static function set_with_tooltip($icon, $tooltip_message, $size = 2, $class = '', $style = '') {
        if (!Strings::contains(HTML::$js_onload, '$(function () {$(\'[data-toggle="popover"]\').popover({trigger: "hover"})});')) {
            HTML::$js_onload .= '$(function () {$(\'[data-toggle="popover"]\').popover({trigger: "hover"})});';
        }
        return '<i data-toggle="popover" data-placement="top" data-content="' . $tooltip_message . '" class="fa fa-' . $icon . ' fa-' . $size . 'x ' . $class . '" ' . ($style == '' ? '' : 'style="' . $style . '"') . '></i>';
    }

    public static function set_checker($condition, $size = 2) {
        return $condition ?Icon::set('check', $size, 'testoverde') : Icon::set('close', $size, 'testorosso');
    }

    public static function set_save() {
        return self::set('save', 1) . ' ';
    }

    public static function set_back() {
        return self::set('arrow-left', 1) . ' ';
    }

    public static function set_next() {
        return self::set('arrow-right', 1) . ' ';
    }

    public static function set_add() {
        return self::set('plus-circle', 1) . ' ';
    }

    public static function set_dash() {
        return self::set('minus', 1) . ' ';
    }

    public static function set_remove() {
        return self::set('trash-alt', 1) . ' ';
    }

    public static function set_edit() {
        return self::set('edit', 1) . ' ';
    }

    public static function set_view() {
        return self::set('eye', 1) . ' ';
    }

    public static function set_user() {
        return self::set('user', 1) . ' ';
    }

    public static function set_hospital() {
        return self::set('hospital-alt', 1) . ' ';
    }

    public static function set_home() {
        return self::set('home', 1) . ' ';
    }

    public static function set_project() {
        return self::set('notes-medical', 1) . ' ';
    }

    public static function set_list() {
        return self::set('list', 1) . ' ';
    }

    public static function set_warning() {
        return self::set('exclamation-triangle', 1, 'text-warning') . ' ';
    }

    public static function set_access_denied() {
        return self::set('ban', 1) . ' ';
    }

    public static function set_label() {
        return self::set('tag', 1) . ' ';
    }

    public static function set_print() {
        return self::set('print', 1) . ' ';
    }

    public static function set_loading() {
        return "<i class='fas fa-cog fa-spin'></i> ";
    }

    public static function set_file() {
        return self::set('file-download', 1) . ' ';
    }

    public static function set_upload() {
        return self::set('file-upload', 1) . ' ';
    }

    public static function set_filter() {
        return self::set('filter', 1) . ' ';
    }

    public static function set_clear() {
        return self::set('broom', 1) . ' ';
    }

    public static function set_refresh() {
        return self::set('sync-alt', 1) . ' ';
    }

    public static function set_lock() {
        return self::set('lock', 1) . ' ';
    }

    public static function set_unlock() {
        return self::set('lock-open', 1) . ' ';
    }

    public static function set_archive() {
        return self::set('archive', 1) . ' ';
    }

    public static function set_output() {
        return self::set('chart-line', 1) . ' ';
    }

    public static function set_sample() {
        return self::set('flask', 1) . ' ';
    }

    public static function set_supply() {
        return self::set('prescription-bottle-alt', 1) . ' '; //first-aid
    }

    public static function set_info() {
        return self::set('info-circle', 1) . ' ';
    }

    public static function set_excel() {
        return self::set('file-excel', 1) . ' ';
    }

    public static function set_laboratory() {
        return self::set('vials', 1) . ' '; //first-aid
    }

}
