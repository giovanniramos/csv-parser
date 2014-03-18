$(function() {
    'use strict';

    // Modal to view the spreadsheet
    function display(file){
        // CSV file
        var csv_file = (file == '' ?  file : '1234planilha.csv');

        // jQuery Modal
        $('.modal')
        .modal({
            remote: 'index.php?spreadsheet=' + csv_file,
            keyboard: true
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
    }

    // jQuery blockUI - Config
    $.blockUI.defaults = {
        overlayCSS:  { backgroundColor:'#000', opacity:0.6 }, 
        css: {  top:'40%', left:'35%', width:'30%', opacity:.5, color:'#fff', border:'none', padding:'15px', backgroundColor:'#000', textAlign:'center', cursor:'wait', '-webkit-border-radius':'10px', '-moz-border-radius':'10px', 'border-radius':'10px' },
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
                    onUnblock: function() {
                        if (json.status === "ok") {
                            // Displays the imported spreadsheet
                            display(json.file_name);
                        }
                    }
                }); 
            }, 2000);
        }
    });

    // Displays the default spreadsheet
    display();

    // Button that submits the information to save in the database
    $(document).on('click', '#csvsave', function() {
        $('.modal').modal('hide');

        $.ajax({
            type: "GET",
            url: "index.php",
            data: { save_data : true },
            dataType: "json",
            success: function(data){
                var message = (data.response == true) ? 'Recording performed successfully.' : data.error;

                $.blockUI({  message: message });
                setTimeout(function() {
                    $.unblockUI(); 
                }, 2000);
            }
        });
    });
});