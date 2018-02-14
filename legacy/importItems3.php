<?php

    include 'dspaceFunctions.php';

    $output = "";
    $Columns = array();

    // *** IMPORTAR METADATOS DE EXCEL ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    importItems($jsessionID);

    $output = "Éxito.";

    function importItems($jsessionID){

        set_time_limit(0);

        $currentDate = getdate(date("U"));
        $dspaceDate = "$currentDate[year]-$currentDate[mon]-$currentDate[mday]";

        require('../spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
        require('../spreadsheet-reader-master/SpreadsheetReader_XLSX.php');

        $Reader = new SpreadsheetReader_XLSX("C:/xampp/htdocs/dspaceapp/Columns.xlsx");
        global $Columns;
        foreach ($Reader as $Row){
            //print_r($Row);
            $Columns = $Row;
        }
        //print_r($Columns);
        postChecklistData($jsessionID);
    }

    function chooseEntidad($entidad){
        switch ($entidad){
            case "Yucatán":
                return "99c2ace2-e19a-47b7-9ba9-7deb4b0819d9";
                break;
            case "Campeche":
                return "14c4f482-7c1a-4fb0-8439-e3578a7f59f0";
                break;
            case "Quintana Roo":
                return "fa8a0b04-5e44-4b02-affa-be1081b2533a";
                break;
            default:
                break;
        }
        return "";
    }

    /**
     * Checklist: Metadatos que se pueden repetir.
     * RadioButton: Metadatos que existen sólo uno de uno mismo.
     */

    function postChecklistData($jsessionID){

        //$ids = getItems($jsessionID);
        //$listOfItems = json_decode($ids,true);
        //var_dump($listOfItems);

        $i = 0;
        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            readChecklist(2, 6, $Row, "arq.Pertenencia", $Row[201], $jsessionID );
            readChecklist(31, 36, $Row, "arq.Epoca", $Row[201], $jsessionID );
            readChecklist(121, 146, $Row, "arq.SisEstructPresbiterio", $Row[201], $jsessionID );
            readChecklist(165, 174, $Row, "arq.Pisos", $Row[201], $jsessionID );
            readChecklist(175, 179, $Row, "arq.Acabados", $Row[201], $jsessionID );
            readChecklist(180, 185, $Row, "arq.Materiales", $Row[201], $jsessionID );
            readChecklist(191, 198, $Row, "arq.Bienes", $Row[201], $jsessionID );
        }
    }

    // uuid leerse desde el excel, que sea el último valor de entrada por ejemplo
    function readChecklist($columnMin, $columnMax, $Row, $key, $uuid, $jsessionID){

        global $Columns;
        $metadata = "";

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){

                $metadata = array( array( 'key' => $key, 'value' => $Columns[$i] ) );
                $jsonItem = json_encode($metadata);
                postMetadataToItem( $jsonItem, $uuid, $jsessionID );
            }
        }
    }

    function readRadioButton($columnMin, $columnMax, $Row){

        global $Columns;

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){
                return $Columns[$i];
            }
        }

        return "";
    }

    echo "<p>".$output."</p>";

?>