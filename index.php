<?php
// Importing the file
if (isset($_FILES['uploadfile'])) {
    require_once 'classes/CSVUpload.class.php';

    new CSVUpload($_FILES['uploadfile']);
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>CSVParser</title>
        <!-- Twitter Bootstrap -->
        <link rel="stylesheet" href="vendor/twitter-bootstrap/css/bootstrap.min.css" media="screen" />
    </head>
    <body>
        <div class="hero-unit" style="margin-top: 110px;">
            <h1>Import to the Database</h1>
            <p>Click "Choose File," locate the .CSV and finally click "Open"</p>
            <p><button id="csvparser" title="Choose File" class="btn btn-primary">Choose File</button></p>
        </div>

        <!-- Modal - Twitter Bootstrap -->
        <div class="modal hide fade" id="viewer">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>Spreadsheet</h3>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel and close</button>
                <button id="csvsave" class="btn btn-primary">Save in Database</button>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/jquery-1.8.0.min.js"><\/script>')</script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/ajaxupload.3.5.js"></script>
        <script src="assets/js/application.js"></script>
        <!-- Twitter Bootstrap -->
        <script src="vendor/twitter-bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>