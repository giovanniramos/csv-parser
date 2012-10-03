<?php
// Loading the class CSVParser
require_once 'classes/CSVParser.class.php';


// Montando a planilha de importação
$csvparser = new CSVParser();
#$csvparser->csv_range(1, 1);

$csvparser->sql_fusion('Nome', 'Sobrenome');
$csvparser->sql_column('Natural')->csv_headers('Natural');

// Importando e gerando planilha de resultados, para gravar em banco
if ($_POST && isset($_POST['spreadsheet'])):
    $path = 'storage/' . $_POST['spreadsheet'];
    $csvparser->csv_import($path)->execute();

    $data['result'] = $csvparser->get_result();
    
    // Montando o Widget CSV para a View
    $data['widget'] = $csvparser->get_csv_table();
endif;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>CSVParser</title>
        <link rel="stylesheet" href="assets/css/screen.css" media="screen" />
    </head>
    <body>
        <?php
        if (isset($data)):
            echo 'output: <pre>', print_r($data, true), '</pre>';
        endif;
        ?>
    </body>
</html>