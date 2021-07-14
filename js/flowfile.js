
var inputs = ["_file", "_prog", "_msg", "_delete", "_preview", "_drop"];

function show_inputs(field, inputs_to_show) {
    for (var i = 0; i < inputs.length; i++) {
        //if (!document.getElementById(inputs[i])) continue;
        var hide = true;
        for (var s = 0; s < inputs_to_show.length; s++) {
            if (inputs[i] == inputs_to_show[s]) {
                hide = false;
            }
        }
        if (hide)
            $("#" + field + inputs[i]).hide();
        else
            $("#" + field + inputs[i]).show();
    }
}

function create_flow(flow_id, accept, upload_on_assign) {
    if (accept === '') {
        window[flow_id].assignBrowse(document.getElementById(flow_id + "_file"), false, true);
        if (document.getElementById(flow_id + "_drop")) window[flow_id].assignDrop(document.getElementById(flow_id + "_drop"), false, true);
    }
    else {
        window[flow_id].assignBrowse(document.getElementById(flow_id + "_file"), false, true, {"accept" : accept});
        if (document.getElementById(flow_id + "_drop")) window[flow_id].assignDrop(document.getElementById(flow_id + "_drop"), false, true, {"accept" : accept});
    }
    
    if (upload_on_assign) {
        window[flow_id].on("filesSubmitted", function(files, event){
            if (files.length === 0) {
                show_inputs(flow_id, ["_file", "_msg", "_drop"]);
                $("#" + flow_id + "_msg").html(msg_print('An error occurred', 'rosso', true)); 
                return;
            }
            show_inputs(flow_id, ["_msg"]);
            $("#" + flow_id + "_msg").html(msg_print('File uploading...', '', true)); 
            window[flow_id].files[0].name = flow_id + (files[0].getExtension() + '' == '' ? '' : '.' + files[0].getExtension());
            window[flow_id].upload();
        });
    }
    
    window[flow_id].on('fileProgress', function(file, chunk){ 
        show_inputs(flow_id, ["_prog", "_msg"]);
        $("#" + flow_id + "_prog_bar").css({width: Math.floor(window[flow_id].progress()*100) + '%'}); 
        $("#" + flow_id + "_prog_txt").html('&nbsp;&nbsp;' + Math.floor(file.progress()*100) + '%'); 
    }); 
    
    window[flow_id].on('fileSuccess', function(file, message, chunk){ 
        try
        {
            var error = JSON.parse(message).error;
            if (error == 'access_denied') {
                login_redirect();
            }
        }
        catch(e)
        {
        }
        if (error) {
            show_inputs(flow_id, ["_file", "_msg", "_drop"]);
            $("#" + flow_id + "_msg").html(msg_print('Error: ' + error, 'rosso', true));  //
        } else {
            show_inputs(flow_id, ["_delete", "_msg", "_preview"]);
            $("#" + flow_id + "_prev").val(message); 
            $("#" + flow_id + "_msg").html(msg_print('File has been uploaded', 'verde', true)); 
//            if (zip_url_redirect != '') {
//                window.location.href = zip_url_redirect;
//            }
        }
    });
    
    window[flow_id].on('error', function(message, file, chunk){ 
        show_inputs(flow_id, ["_file", "_msg", "_drop"]);
        $("#" + flow_id + "_msg").html(msg_print('Error: ' + message, 'rosso', true));  //
    });
}

function msg_print(msg, color, bold) {
    return '<span class="' + (color != '' ? 'testo' + color : '') + '" style="' + (bold ? 'font-weight: bold' : '') + '">' + msg + '</span>';
}



