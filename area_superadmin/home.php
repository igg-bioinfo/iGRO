<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$html .= '<div style="text-align: center">';
foreach (Menu::SA_MENU as $saMenu) {
    $html .= HTML::set_button(Language::find($saMenu[1]), '', URL::create_url($saMenu[0]), '', "width: 300px; font-size: 18px;").HTML::set_br(2);
}
$html .= '</div>';



//--------------------------------------HTML
HTML::$title = Language::find('home');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));