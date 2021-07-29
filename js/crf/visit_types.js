
function visit_types_fail(error){
    $('#table_error').show();
    $('#table_text_error').html(error + '');
}

function visit_types_done(data){
    $("#btn_save").show();
    $('#table_error').hide();
    $('#table_text_error').html('');
    if (data.error) {
        if (data.error == 'access_denied') {
            login_redirect();
        }
        else {
            $("#btn_save").hide();
            $('#table_error').show();
            $('#table_text_error').html(data.error);
            $("#vtype_html").html('');
        }
        return;
    }
    if(data.vtypes.length == 0) {
        $("#vtype_html").html('');
        $("#btn_save").hide();
        $('#table_error').show();
        $('#table_text_error').html(NO_TYPE);
        return;
    }
    var type_id = data.suggested ? data.suggested.id : 0;
    var select_id = 'select_vtype';
    var no_type = type_id == 0 || !type_id;
    var vtypes = "<table class=\'table\'>";
    vtypes += "<tr>"; 
    vtypes += "<td style='width: 10px'>"; 
    vtypes += TRANS_TYPE + ":"; 
    vtypes += "</td>";  
    vtypes += "<td style='width: 200px'>"; 
    vtypes += "<div class='form-group has-feedback " + (no_type ? "" : "has-success") + "'>";
        vtypes += "<div id='status_" + select_id + "'>";
            vtypes += "<select id='" + select_id + "' name='" + select_id + "' class='form-control' style='width: 300px;' " + (no_type ? "disabled" : "") 
                + " onblur='check_select(\"" + select_id + "\");' onchange='check_select(\"" + select_id + "\");'>"; 
                vtypes += "<option value='0'>" + TRANS_SELECT + "</option>"; 
                for (var t = 0; t < data.vtypes.length; t++) {
                    vtypes += "<option value='" + data.vtypes[t].id + "' " + (type_id === data.vtypes[t].id ? "selected='selected'" : "") + ">"; 
                    vtypes += data.vtypes[t].name; 
                    vtypes += "</option>"; 
                }
            vtypes += "</select>"; 
            vtypes += "<div id='error_" + select_id + "'></div>";
        vtypes += "</div>";
    vtypes += "</div>";
    vtypes += "</td>"; 
    vtypes += "</tr>"; 
    vtypes += "</table>"; 
    vtypes += "<script>check_select('" + select_id + "')</script>";
    //    alert(vtype_blocks);
    $("#vtype_html").html(vtypes);
}
