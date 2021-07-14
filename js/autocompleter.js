
function init_autocompleter(input_text_id, hidden_text_id, data_source_url, is_mandatory, min_length){
    // Initializes jQuery UI autocomplete widget
    $('#'+input_text_id).autocomplete({
        source: function( request, response ) {
            $.ajax( {
                url: data_source_url + request.term,
                method: "get",
                dataType: "json",
                success: function( data ) { 
                    if (is_mandatory) {
                        show_auto_error(input_text_id, AUTO_NO_SELECTED);
                    }
                    if (data.error == 'access_denied'){
                        login_redirect();
                    }
                    else if (data.error == AUTO_NOT_FOUND){
                        if (is_mandatory)
                            show_auto_error(input_text_id, data.error);
                    }
                    else if (data.error !== undefined && data.error != ''){
                        show_auto_error(input_text_id, data.error);
                    }
                    else 
                        response( data );
                    $('#' + input_text_id).removeClass('ui-autocomplete-loading');
                },
                error: function( data ) {
                    show_auto_error(input_text_id);
                    $('#' + input_text_id).removeClass('ui-autocomplete-loading');
                }
            } );
        },
        delay: 300,
        minLength: min_length,
        search: function(event, ui){
            reset_hidden_value(hidden_text_id);
            reset_error_message(input_text_id);
        },
        response: function(event, ui){
        },
        open: function(){
            var dropdown_widget_id = $(this).autocomplete("widget").attr("id");
            highlight_terms(input_text_id, dropdown_widget_id);
        },
        select: function(event, ui){
            run_select_action(input_text_id, hidden_text_id, ui, event);
        }
    });
    $('#'+input_text_id).keypress(function(event) {
        auto_input_handler(event);
    });   
}

var reset_error_message = function(input_text_id){
    change_status(input_text_id, 1);
};

var reset_hidden_value = function (hidden_text_id){
    $('#' + hidden_text_id).val('');
};

var highlight_terms = function (input_text_id, ul_id){
    var keywords = $('#' + input_text_id).val();
    var options = {};
    if (keywords != AUTO_NOT_FOUND){
        $('#' + ul_id + ' .ui-menu-item-wrapper').mark(keywords, options);
    }
};

var run_select_action = function (input_text_id, hidden_text_id, ui, evt){    
    var label = ui.item.value;
    var value = ui.item.id;
    
    if (label != AUTO_NOT_FOUND){
        $('#' + input_text_id).val(label);
        $('#' + hidden_text_id).val(value);
        change_status(input_text_id, 1);
    }
    else{
        $('#' + input_text_id).val('');
        $('#' + input_text_id).trigger( "blur" );
        
        evt.preventDefault();
    }
};

var auto_input_handler = function (e) {
    var evt = e || window.event;
    var key = '';
    var regex = AUTO_PATTERN;
 
    if (evt.key == "Backspace" || evt.key == "Delete") return true;
 
    key = evt.keyCode || evt.which;
    key = String.fromCharCode(key);
    
    if(!regex.test(key)){
        evt.returnValue = false;
        evt.preventDefault();
    }
}

var show_auto_error = function (input_text_id, error){
    change_status(input_text_id, 2, error === undefined ? AUTO_ERROR : error);
};

var check_auto_error = function (input_text_id, hidden_text_id, min_length){
    var input = $('#' + input_text_id).val();
    var hidden = $('#' + hidden_text_id).val();
    var error = $('#error_' + input_text_id).html();
    if (error.length > 7) {
    }
    else if (hidden == '' && input.length < min_length) {
        change_status(input_text_id, 2, String.format(TEXT_MIN_LENGHT, min_length));
    }
    else if (hidden == '') {
        change_status(input_text_id, 2, AUTO_NO_SELECTED);
    }
    else {
        change_status(input_text_id, 1, '');
    }
};

//function validate_auto_input(input_text_id, hidden_text_id){    
//    var value = $('#' + hidden_text_id).val();
//    var text_check_status = check_text(input_text_id, 3);
//    var loading_in_progress = $('#' + input_text_id).hasClass('ui-autocomplete-loading');
//    if (text_check_status && !loading_in_progress && value.length == 0){
//        change_status(input_text_id, 2, AUTO_INCORRECT);
//    };
//};


