<?php
// Starting upload
if (isset($_FILES['uploadfile'])):
    require_once 'classes/Upload.class.php';

    new Upload($_FILES['uploadfile']);
    exit;
endif;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>CSVParser</title>
        <!-- Twitter Bootstrap -->
        <link rel="stylesheet" href="vendor/twitterbootstrap/css/bootstrap.min.css" media="screen" />
    </head>
    <body>
        <div class="hero-unit" style="margin-top: 110px;">
            <h1>Importing to the Database</h1>
            <p>Allowed extensions: .xls, .xlsx, .csv</p>
            <p><button id="csv_import" title="Select" class="btn btn-primary">Select file</button></p>
        </div>
        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/jquery-1.8.0.min.js"><\/script>')</script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/ajaxupload.3.5.js"></script>
        <script src="assets/js/application.js"></script>
        <!-- Twitter Bootstrap -->
        <script src="vendor/twitterbootstrap/js/bootstrap.min.js"></script>
    </body>
</html>