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
    new AjaxUpload($('[id="csvparser"]'), 
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
                            //window.showModalDialog('viewer.php?spreadsheet=' + json.file_name, 'win','dialogHeight:800px;dialogWidth:600px;help:no;status:no;scroll:no;menubar=no;resize:no;resizable=no;center=yes;');
                            window.showModalDialog('viewer.php?spreadsheet=' + json.file_name, 'win', 'dialogWidth:800px;dialogHeight:600px;help:no;status=no;scroll=no;menubar=no;resizable=no;center=yes');
                        }
                    } 
                }); 
            }, 2000);
        }
    });

    // Modal Config
    $('.modal')
    .modal({
        keyboard: true,
        remote: 'viewer.php?spreadsheet=1234planilha.csv'
    })
    .css({
        'height': function() { return ($(document).height() * .8) + 'px'; },
        'width': function() { return ($(document).width() * .9) + 'px'; },
        'margin-top': function() { return -($(this).height() / 2); },
        'margin-left': function() { return -($(this).width() / 2); }
    });
    $('.modal-body')
    .css({
        'min-height' : function() { return ($('.modal').height() - 140) + 'px'; },
    });
});

