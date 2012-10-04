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
    var valums_button = $("[id='csv_import']");
    new AjaxUpload(valums_button, 
    {
        name: 'uploadfile',
        action: 'index.php',
        autoSubmit: true,
        responseType: 'json',
        onComplete: function(file, json) {
            $.blockUI({ 
                message: json.message
            });
            setTimeout(function() {
                $.unblockUI({
                    onUnblock: function(){ 
                        if (json.status === "ok"){
                            // Create the form on the fly
                            form = '<form action="importing.php" method="post"><input name="spreadsheet" value="' + json.file_name + '" /></form>';
                            $(form).appendTo("body").submit();

                            //$('<form/>').attr('action','form2.html').submit();
                        }
                    } 
                }); 
            }, 2000);
        }
    });

});