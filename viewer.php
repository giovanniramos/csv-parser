<?php

// Saving the file imported into the database
if (isset($_GET['spreadsheet'])) {
    require_once 'classes/CSVParser.class.php';

    // Riding the spreadsheet import
    $csvparser = new CSVParser();
    #$csvparser->csv_range(2, 2);
    $csvparser->sql_fusion('status', 1);
    $csvparser->sql_column('fullname')->csv_headers('firstname, lastname', ' ');
    $csvparser->sql_column('email')->csv_headers('email');

    // Import and prepare the result to recording in database
    $csvparser->csv_import('storage/' . $_GET['spreadsheet']);
    $csvparser->execute();

    // Result for recording
    $sql_result = $csvparser->get_result();

    // Result for viewing
    $cvs_result = $csvparser->get_csv_table();

    echo '<pre style="height: 150px; overflow: auto;">', print_r($sql_result, true), '</pre>';
    echo '', print_r($cvs_result, true), '';
}