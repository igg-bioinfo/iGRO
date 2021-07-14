// *************************** VAS slider utils  ***************************************

var slider_is_right = false;

// Use this function to validate slider value.
// Function "check_status" does not work correctly as it cannot find objects which include dot character inside id values (e.g. id="VAS_WB_0.5" )
// Below jquery uses lookup by name, instead of id, and it works.
function check_radio_slider(slider_div_id, radio_name) {
    var $radios = $('input[type=radio][name=' + radio_name + ']');
    var $slider_type_obj = $('input[type=hidden][name=' + radio_name + '_type]');
    var $slider_div = $('#' + slider_div_id);
    var disabled = $slider_div.slider('option', 'disabled');

    // do not validate if slider is disabled
    if (disabled)
        return;

    check_radio_result = check_radio(radio_name);

    // assign slider type value if slider type empty and radio selected
    if ($slider_type_obj.val() == 0 && check_radio_result) {
        $slider_type_obj.val(1);
    }

    set_slider_required_status(slider_div_id, radio_name, !check_radio_result);
    return check_radio_result;
}

// *************************** SLIDER RELATED FUNCTIONS ***************************************
function slider_rtl_mapper(value) {
    if (slider_is_right) {
        return 10 - value;
    }
    else {
        return value;
    }
}

// Main slider initialization function, should be called on each page including slider object.
// Arguments:
// - slider_div_id:  html/css id of div object that slider will be attached to
// - field_name: radio input field name on html form
function init_vas_slider(slider_div_id, field_name) {
    // find first child div inside main div
    var slider_handle_id = $('#' + slider_div_id).children().first('div').attr('id');
    var $slider_handle = $('#' + slider_handle_id);
    var slider_selection = $slider_handle.data('selection');
    var $radio_handle = $('input:radio[name=' + field_name + ']');
    var $slider_type_obj = $('<input>').attr({type: 'hidden', name: field_name + '_type', value: 0});
    var slider_status = $slider_handle.data('status');

    // attach hidden input field to main div, it is used to store slider type (1=radios, 2=slider)
    $slider_type_obj.appendTo('#' + slider_div_id);

    $('#' + slider_div_id).slider({
        value: 5,
        min: 0,
        max: 10,
        step: 0.5,
        create: function () {
            $this = $(this);

            if ($.isNumeric(slider_selection)) {
                // show slider value if value present
                $slider_handle.text(slider_selection);
                $this.slider('value', slider_rtl_mapper(slider_selection));
            }
            else {
                // hide when no initial data present
                $slider_handle.hide();
            }

            // determine slider status
            if (slider_status == 'readonly') {
                $slider_handle.show();
                $this.slider('option', 'disabled', true);
            }
            // if slider was initialized as disabled, but now has a value, it should not be disabled anymore
            else if (slider_status == 'disabled' && !$.isNumeric(slider_selection)) {
                disable_slider(slider_div_id, field_name);
            }
            else {
                add_vas_radio_action($radio_handle, slider_div_id, slider_handle_id);
            }

        },
        slide: function (event, ui) {
            $slider_handle.show();
            var slider_value = slider_rtl_mapper(ui.value);
            $slider_handle.text(slider_value);
            select_vas_radio_value(slider_value, $radio_handle);

            // make slider not required after selection has been made
            set_slider_required_status(slider_div_id, field_name, false);

            // assign slider type value if slider type empty
            if ($slider_type_obj.val() == 0) {
                $slider_type_obj.val(2);
            }
        },
    });
}

function add_vas_radio_action($radio_handle, slider_div_id, slider_handle_id) {
    $radio_handle.click(function () {
        var $slider = $('#' + slider_div_id);
        var $slider_handle = $('#' + slider_handle_id);
        var radio_checked_value = $(this).val();

        $slider.slider('value', slider_rtl_mapper(radio_checked_value));
        change_slider_color(slider_div_id, '');
        $slider.removeClass('basic invalid-feedback');
        $slider_handle.text(radio_checked_value);
        $slider_handle.show();
    });
}

function select_vas_radio_value(slider_value, $radio_handle) {
    var select_item = slider_value / 0.5;

    $radio_handle[select_item].checked = true;
}

function change_slider_color(slider_div_id, new_color) {
    var $widget = $('#' + slider_div_id).slider('widget');

    // if color is not defined, restore original color
    if (!new_color)
        new_color = '#c5c5c5';
    $widget.css('border-color', new_color);
}

function disable_slider(slider_div_id, radio_name) {
    var $radios = $('input[type=radio][name=' + radio_name + ']');
    var $slider_div = $('#' + slider_div_id);
    $slider_div.slider('option', 'disabled', true);
    $slider_div.slider('option', 'classes.ui-slider', 'custom-slider-disabled');
    $radios.prop('disabled', true);
}

function enable_slider(slider_div_id, radio_name) {
    var $radios = $('input[type=radio][name=' + radio_name + ']');
    var $slider_div = $('#' + slider_div_id);
    $slider_div.slider('option', 'disabled', false);
    $slider_div.slider('option', 'classes.ui-slider', '');
    $radios.prop('disabled', false);
}

function set_slider_required_status(slider_div_id, radio_name, is_required) {
    var $radios = $('input[type=radio][name=' + radio_name + ']');
    var $slider_div = $('#' + slider_div_id);

    // required
    if (is_required) {
        $radios.parent('.form-check-label').find('.radio_checkmark').css('border', 'solid red 1px').addClass('invalid-feedback basic');
        change_slider_color(slider_div_id, 'red');
        // additional class added (slider-visible), so slider in red would stay visible after adding "invalid-feedback" class
        $slider_div.addClass('basic invalid-feedback slider-visible');
    }
    // not required
    else {
        // when calling as slider action (is_required set to "false"), remove both: red border and invalid feedback class
        $radios.parent('.form-check-label').find('.radio_checkmark').css('border', '').removeClass('invalid-feedback');
        change_slider_color(slider_div_id, '');
        $slider_div.removeClass('basic invalid-feedback');
    }
}

function clear_slider_selection(slider_div_id, radio_name) {
    var $radios = $('input[type=radio][name=' + radio_name + ']');
    var $slider_div = $('#' + slider_div_id);
    var slider_handle_id = $('#' + slider_div_id).children().first('div').attr('id');
    var $slider_handle = $('#' + slider_handle_id);

    $slider_handle.hide();
    $slider_div.slider('value', '');
    $radios.prop('checked', false);
}