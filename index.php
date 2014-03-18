<?php
// Initializes the session
session_start();

// Importing a spreadsheet
if (isset($_FILES['uploadfile'])) {
    require_once 'classes/CSVUpload.class.php';

    new CSVUpload($_FILES['uploadfile']);

    exit;
}

// Riding the imported spreadsheet
if (isset($_GET['spreadsheet'])) {
    require_once 'classes/CSVParser.class.php';

    $csvparser = new CSVParser();

    /**
     * dba_fusion: Sets a column in the database with a the preset value
     * - PARAM-1: NAME OF THE DATABASE COLUMN
     * - PARAM-2: VALUE PRE-DEFINED
     */
    $csvparser->dba_fusion('date_created', date('Y-m-d H:i:s'));

    /**
     * dba_column: Sets a column in the database that will store the data from a spreadsheet column
     * - PARAM-1: NAME OF THE DATABASE COLUMN
     * 
     * csv_headers: Sets the columns of the spreadsheet for each column in the database
     * - PARAM-1: NAME OF ONE OR MORE COLUMNS IN THE SPREADSHEET , SEPARATED BY COMMAS
     * - PARAM-2: SEPARATOR
     */
    $csvparser->dba_column('fullname')->csv_headers('firstname, lastname', ' ');
    $csvparser->dba_column('email')->csv_headers('email');

    // Sets the start and end of data capture
    #$csvparser->csv_range(3, 4);

    // Prepares the spreadsheet for reading fields 
    // and returns the result to the conference
    $result = $csvparser->execute('storage/' . $_GET['spreadsheet']);

    // Dataset to save the database
    $_SESSION['dataset'] = $csvparser->get_dataset();

    echo '<pre style="height: 150px; overflow: auto;">';
    echo print_r($_SESSION['dataset'], true);
    echo '</pre>';

    // Results to display
    echo $result;

    exit;
}

// Saves in the database via Ajax
if (isset($_GET['save_data'])) {

    // Configuration data from the database connection
    $db_host = 'localhost';
    $db_base = 'csvparser';
    $db_user = 'root';
    $db_pass = '';

    // Performs a connection to the database via PDO
    $dbh = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_base, $db_user, $db_pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $dbh->query("SET NAMES UTF8");

    // Checks if an Ajax request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Transaction start
        $dbh->beginTransaction();

        try {
            // Clause of inserting
            $stmt = $dbh->prepare("INSERT INTO spreadsheet (date_created, fullname, email) VALUES (?, ?, ?)");

            $dataset = $_SESSION['dataset'];
            foreach ($dataset as $row) {
                $stmt->bindValue(1, $row['date_created']);
                $stmt->bindValue(2, $row['fullname']);
                $stmt->bindValue(3, $row['email']);
                $stmt->execute();
            }

            // Transaction commit
            $dbh->commit();

            echo json_encode(array(
                'response' => true
            ));
        } catch (\PDOException $e) {
            // Transaction rollback
            $dbh->rollBack();

            echo json_encode(array(
                'response' => false,
                'error' => $e->getMessage()
            ));
        }
    }

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

        <script src="//oss.maxcdn.com/jquery/1.8.3/jquery-1.8.3.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/jquery-1.8.3.min.js"><\/script>');</script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/ajaxupload.3.5.js"></script>
        <script src="assets/js/application.js"></script>
        <!-- Twitter Bootstrap -->
        <script src="vendor/twitter-bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>