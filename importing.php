<?php
// Loading the class CSVParser
require_once 'classes/CSVParser.class.php';

// Riding the spreadsheet import
$csvparser = new CSVParser();
#$csvparser->csv_range(1, 1);
$csvparser->sql_column('fullname')->csv_headers('Nome, Sobrenome', ' ');
$csvparser->sql_column('nationality')->csv_headers('Natural');
$csvparser->sql_fusion('nacionalidade', 'Brasileira');

if (isset($_POST['spreadsheet'])):
    // Import and prepare the result to recording in database
    $csvparser->csv_import('storage/' . $_POST['spreadsheet'])->execute();

    // Result for recording
    $data['sql_result'] = $csvparser->get_result();

    // Result for viewing
    $data['cvs_result'] = $csvparser->get_csv_table();
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
        <?php
        if (isset($data)):
            echo 'sql_result: <pre>', print_r($data['sql_result'], true), '</pre>';
            echo 'cvs_result: ', print_r($data['cvs_result'], true), '';
        endif;
        ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/jquery-1.8.0.min.js"><\/script>')</script>
        <!-- Twitter Bootstrap -->
        <script src="vendor/twitterbootstrap/js/bootstrap.min.js"></script>
    </body>
</html>