$(function() {

   // jQuery blockUI - Config
    $.blockUI.defaults = {
        overlayCSS:  { 
            backgroundColor: '#000', 
            opacity: 0.6 
        }, 
        css: { 
            top: '40%', 
            left: '35%', 
            width: '30%', 
            opacity: 0.6, 
            opacity: .5, 
            color: '#fff', 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            'border-radius':'10px', 
            textAlign: 'center',
            cursor: 'wait' 
        },
        showOverlay: true
    }; 


    // Valums - Ajax upload
    var valums_button = $("button[id='csv_import']");
    var valums_submit = $("form[id='csv_submit']");
    var valums_uploader = new AjaxUpload(valums_button, 
    {
        name: 'uploadfile',
        action: 'upload.php',
        autoSubmit: true,
        responseType: 'json',
        onComplete: function(file, json) {
            if (json.status === "error") {
                _message = '<h3>Unable to load file: ' + file + '</h3>';
                _timeout = 2000;
            } else {
                _message = '<h3>Loading file: ' + file + '</h3>';
                _timeout = 1500;
                $("[name='spreadsheet']").val(json.fileName);
            }

            $.blockUI({ 
                message: _message
            });
            setTimeout(function() {
                $.unblockUI({
                    onUnblock: function(){ 
                        if (json.status === "ok") valums_submit.submit();
                    } 
                }); 
            }, _timeout);
        }
    });

});