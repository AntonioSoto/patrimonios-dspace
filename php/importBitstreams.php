<?php

    include 'dspaceFunctions.php';

    $output = "";

    // *** IMPORTAR BITSTREAMS DE EXCEL ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    importBitstreams($jsessionID);

    $output = "Éxito.";

    function importBitstreams($jsessionID){

        set_time_limit(0);

        require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
        require('spreadsheet-reader-master/SpreadsheetReader_XLSX.php');

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            //echo "$Row[0], $Row[1], $Row[2], $Row[3]";
            if($Row[3] != ""){
                uploadPhotoExcel($Row[0],$Row[1],$Row[2],$Row[3],$jsessionID);
            }
        }
    }

    echo "<p>".$output."</p>";

?>