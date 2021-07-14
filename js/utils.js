function create_hidden_field(name, value, form_id) {
    if (!form_id) {
        form_id = 'form1';
    }
    if (!$('#' + form_id).length) {
        throw 'Form does not exist!';
    }

    $('<input>').attr({
        type: 'hidden',
        name: name,
        value: value
    }).appendTo($('#' + form_id));
}

function post_button_click(fields, form_id) {
    if (!form_id) {
        form_id = 'form1';
    }

    if (fields) {
        try {
            var fields_obj = JSON.parse(fields);
            var keys = Object.keys(JSON.parse(fields));
        } catch (e) {
            var fields_obj = fields.data;
            var keys = Object.keys(fields.data);
        }
        $(keys).each(function (key, value) {
            create_hidden_field(keys[key], fields_obj[keys[key]]);
        });
    }
    page_validation(form_id);
}

function check_date_range(id, required, id_from, id_to) {
    if (id_from) {
        var min = $('#' + id_from).val();
    }
    if (id_to) {
        var max = $('#' + id_to).val();
    }
    return check_date_min_max(id, required, min, max);
}

function check_checkbox_as_radio(group1, group2, error) {
    var is_group_checked = false;
    var is_group2_checked = false;
    for (var i = 0; i < group1.length; i++) {
        if ($('#' + group1[i]).is(':disabled')) {
            if (error) {
                var error = $('#' + error);
                set_error(error, '');
                error.removeClass('invalid-feedback');
            }
            return;
        }
        if ($('#' + group1[i]).prop('checked')) {
            is_group_checked = true;
        }
    }
    for (var i = 0; i < group2.length; i++) {
        if ($('#' + group2[i]).prop('checked')) {
            is_group2_checked = true;
        }
    }
    if (is_group2_checked) {
        group1.forEach(function (item) {
            change_status(item, 4, '');
        });
    } else {
        group1.forEach(function (item) {
            change_status(item, 0, '');
        });
    }
    if (is_group_checked) {
        group2.forEach(function (item) {
            change_status(item, 4, '');
        });
    } else {
        group2.forEach(function (item) {
            change_status(item, 0, '');
        });
    }

    check_checkboxes_array(group1, 1, error);
    check_checkboxes_array(group2, 1, error);

}

function specify_check(controller, input_to_be_enabled, controller_value, input_to_be_disabled, trigger_handlers) {
    var selected_value = get_selected_value(controller);

    input_to_be_enabled = parse_array(input_to_be_enabled);

    if (input_to_be_disabled) {
        input_to_be_disabled = parse_array(input_to_be_disabled);
    }

    if (!controller_value) {
        controller_value = ['1'];
    } else {
        controller_value = parse_array(controller_value);
    }

    if (controller_value.indexOf(selected_value) !== -1) { // enable
        $.each(input_to_be_enabled, function (index, value) {
            var type = get_type(value);
            switch (type) {
                case 'radio':
                    $("input[name='" + value + "']").each(function () {
                        change_status(this.id, 0, '');
                    });

                    // for radio, trigger validation for checked radio button if exists otherwise for the first radio button
                    var radio_input = $("input[name='" + value + "']:checked");
                    if (radio_input.length) {
                        radio_input.trigger('onclick');

                        if (trigger_handlers) {
                            radio_input.triggerHandler('click');
                        }
                    } else {
                        $("input[name='" + value + "']:first").trigger('onclick');

                        if (trigger_handlers) {
                            $("input[name='" + value + "']:first").triggerHandler('click');
                        }
                    }

                    break;
                case 'multiselect':
                    change_status(value, 0, '');
                    var input = $('#' + value)
                    input.selectpicker('refresh');

                    if (trigger_handlers) {
                        input.triggerHandler('change');
                    }
                    break;
                default:
                    change_status(value, 0, '');
                    // when enabled, trigger validation on controlled inputs
                    var input = $('#' + value);
                    if (input.attr('onkeyup')) {
                        input.trigger('onkeyup');
                    } else if (input.attr('onchange')) {
                        input.trigger('onchange');
                    } else if (input.attr('onclick')) {
                        input.trigger('onclick');
                    } else if (input.attr('onblur')) {
                        input.trigger('onblur');
                    }

                    if (trigger_handlers) {
                        input.triggerHandler('click');
                    }
                    break;
            }
        });

        $.each(input_to_be_disabled, function (index, value) {
            disable_control(value, controller, trigger_handlers);
        });
    } else { // disable
        $.each(input_to_be_enabled, function (index, value) {
            disable_control(value, controller, trigger_handlers);
        });
    }
}

function validate_and_specify_check(controller, input_to_be_enabled, controller_value, input_to_be_disabled, trigger_handlers) {
    // same as specify_check, but before validate controller if it is enabled
    var type = get_type(controller);
    switch (type) {
        case 'radio':
            if ($("input[name='" + controller + "']:enabled").length) {
                check_radio(controller);
            }
            break;
        case 'checkbox':
            if ($('#' + controller).is(':enabled')) {
                check_checkbox(controller, 1);
            }
            break;
        case 'SELECT':
            if ($('#' + controller).is(':enabled')) {
                check_select(controller);
            }
            break;
        case 'text':
        case 'TEXTAREA':
            if ($('#' + controller).is(':enabled')) {
                check_text(controller);
            }
            break;
        default:
            break;
    }

    specify_check(controller, input_to_be_enabled, controller_value, input_to_be_disabled, trigger_handlers);
}

function disable_control(value, controller, trigger_handlers) {
    var type = get_type(value);
    var input = $('#' + value);
    switch (type) {
        case 'checkbox':
            empty_input(value);
            change_status(value, 4, '');
            $('#error_specify_' + controller).css('visibility', 'hidden');
            break;
        case 'radio':
            $("input[name='" + value + "']").prop('checked', false);
            $("input[name='" + value + "']").each(function () {
                change_status(this.id, 4, '');
            });

            input = $("input[name='" + value + "']:first");

            break;
        case 'multiselect':
            input.selectpicker('val', []);
            change_status(value, 4, '');
            $("[data-id='" + value + "']").css('border-color', '#ced4da');
            input.selectpicker('refresh');
            break;
        case 'text':
        case 'TEXTAREA':
        default:
            empty_input(value);
            change_status(value, 4, '');
            break;
    }

    // on disable only validation on controlled items must be triggered

    var event;
    if (input.attr('onkeyup')) {
        event = 'onkeyup';
    } else if (input.attr('onchange')) {
        event = 'onchange';
    } else if (input.attr('onclick')) {
        event = 'onclick';
    } else if (input.attr('onblur')) {
        event = 'onblur';
    }

    if (event && (input.attr(event).indexOf('specify_check') !== -1 || input.attr(event).indexOf('validate_and_specify_check') !== -1)) {
        input.trigger(event);
    }

    if (trigger_handlers) {
        if (input[0].onclick) {
            input.triggerHandler('click');
        }
    }
}

function get_selected_value(controller) {
    controller = parse_array(controller);

    var selected_value;
    if (controller.length) {
        $.each(controller, function (index, value) {
            var type = get_type(value);
            if (type === 'text') {
                if ($('#' + value).val().length > 0) {
                    selected_value = '1';
                    return false;
                } else {
                    selected_value = '0';
                    return false;
                }
            }

            if (type === 'SELECT') {
                selected_value = $('#' + value).val();
                return false;
            }

            if ($("input[name='" + controller + "']").prop('disabled')) {
                selected_value = 'disabled';
                return false;
            }
            selected_value = $("input[name='" + value + "']:checked").val();
            if (selected_value === '1') {
                return false;
            }
        });
    } else {
        throw 'No controllers';
    }

    return selected_value ? selected_value : '0';
}

function parse_array(array_to_parse) {
    if (Array.isArray(array_to_parse)) {
        return array_to_parse;
    }
    try {
        return JSON.parse(array_to_parse);
    } catch (e) {
        return array_to_parse.split(';');
    }
}

function get_type(input) {
    var type;
    if ($('#' + input).length) {
        type = $('#' + input).attr('type');
        if (!type) {
            type = $('#' + input).prop('tagName');
        }
        if (type === 'SELECT' && $("#" + input).prop('multiple')) {
            type = 'multiselect';
        }
        if (type === 'SELECT' && $("#" + input).parent().attr('class').includes('bootstrap-select')) {
            type = 'multiselect';
        }
    } else {
        type = $("input[name='" + input + "']").attr('type');
    }

    return type;
}

function show_modal(modal_id, message, on_click_function, data) {
    var modal = $('#' + modal_id);
    if (message) {
        if (Array.isArray(message)) {
            modal.find('p').html(decodeURIComponent(message[0].replace(/\+/g, ' ')));
            modal.find('h4').html(decodeURIComponent(message[1].replace(/\+/g, ' ')));
        } else {
            modal.find('p').html(decodeURIComponent(message.replace(/\+/g, ' ')));
        }

    }
    modal.find('.btn-danger').unbind();
    if (typeof on_click_function === 'string') {
        on_click_function = window[on_click_function];
    }
    if (data) {
        data = JSON.parse(data);
        modal.find('.btn-danger').click(data, on_click_function);
    } else {
        modal.find('.btn-danger').click(on_click_function);
    }
    modal.modal('show');
}

function show_delete_modal(id_to_delete, label, action, modal_id) {
    if (!modal_id) {
        modal_id = 'confirm_modal';
    }

    var on_click_function = function () {
        if (!action) {
            action = 'delete';
        }
        create_hidden_field('post_act', action);
        create_hidden_field('id_to_delete', id_to_delete);
        $('#form1').submit();
    };

    show_modal(modal_id, label, on_click_function);
}

function show_confirm_modal(url, label, modal_id) {
    if (!modal_id) {
        modal_id = 'confirm_modal';
    }

    var on_click_function = function () {
        location.href = url;
    };

    show_modal(modal_id, label, on_click_function);
}

function show_post_confirm_modal(label, fields, form_id, modal_id) {
    if (!modal_id) {
        modal_id = 'confirm_modal';
    }

    if (!form_id) {
        form_id = 'form1';
    }

    var on_click_function = function () {
        post_button_click(fields, form_id);
    };

    show_modal(modal_id, label, on_click_function);
}

function empty_input(input_id) {
    var type = get_type(input_id);
    var input = $('#' + input_id);
    switch (type) {
        case 'radio':
            input.prop('checked', false);
            break;
        case 'checkbox':
            input.prop('checked', false);
            break;
        case 'SELECT':
            input.prop('selectedIndex', 0);
            break;
        case 'text':
        case 'TEXTAREA':
            input.val('');
            break;
        default:
            break;
    }
}

function browser_check() {
    var parser = new UAParser();

    var result = parser.getResult();
    var is_good_browser = true;

    switch (result.browser.name) {
        case 'Chrome':
            if (result.browser.major < 58) {
                is_good_browser = false;
            }
            break;
        case 'Chrome Headless':
            if (window.location.href.indexOf('test') === -1) {
                is_good_browser = false;
            }
            break;
        case 'Firefox':
            if (result.browser.major < 54) {
                is_good_browser = false;
            }
            break;
        case 'IE':
            if (result.browser.major < 11) {
                is_good_browser = false;
            }
            break;
        case 'Safari':
        case 'Mobile Safari':
            if (result.browser.major < 10) {
                is_good_browser = false;
            }
            break;
        default:
            is_good_browser = false;
            break;
    }

    if (!is_good_browser) {
        $('#form1').replaceWith('<div class="row justify-content-center" style=""><div class="col-md-6  alert alert-danger text-center" style="font-size:20px"><i class="fas fa-exclamation-circle fa-lg pr-2"></i>Your browser is not supported!<br/>' + result.browser.name + '</div></div>');
    }
}
