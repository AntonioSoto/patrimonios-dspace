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

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {
            //print_r($Row);
            $params['body'] = array(
                'metadata' => array(
                    array( 'key' => 'dc.creator', 'value' => $Row[9] ),
                    array( 'key' => 'dc.title', 'value' => $Row[0] ),
                    array( 'key' => 'dc.date', 'value' => $dspaceDate ),
                    array( 'key' => 'arq.Nombre', 'value' => $Row[0] ),
                    array( 'key' => 'arq.Direccion', 'value' => $Row[1] ),
                    // arq.Pertenencia es una Checklist
                    array( 'key' => 'arq.Localidad', 'value' => $Row[7] ),
                    array( 'key' => 'arq.Municipio', 'value' => $Row[8] ),
                    array( 'key' => 'arq.Entidad', 'value' => $Row[9] ),
                    array( 'key' => 'arq.Jurisdiccion', 'value' => readRadioButton(10, 16, $Row) ),
                    array( 'key' => 'arq.CategoriaActual', 'value' => readRadioButton(17, 23, $Row) ),
                    array( 'key' => 'arq.CategoriaOriginal', 'value' => readRadioButton(24, 30, $Row) ),
                    // arq.Epoca es una Checklist
                    array( 'key' => 'arq.Anio', 'value' => $Row[37] ),
                    array( 'key' => 'arq.UsoActual', 'value' => readRadioButton(38, 40, $Row) ),
                    array( 'key' => 'arq.Fundador', 'value' => $Row[41] ),
                    array( 'key' => 'arq.Constructor', 'value' => $Row[42] ),
                    array( 'key' => 'arq.EstadoConservacion', 'value' => readRadioButton(43, 45, $Row) ),
                    array( 'key' => 'arq.Tipologia', 'value' => readRadioButton(46, 50, $Row) ),
                    array( 'key' => 'arq.TipologiaArqui', 'value' => readRadioButton(51, 72, $Row) ),
                    array( 'key' => 'arq.Muros', 'value' => readRadioButton(73, 75, $Row) ),
                    array( 'key' => 'arq.SistemaCubierta', 'value' => readRadioButton(76, 77, $Row) ),
                    array( 'key' => 'arq.SisEstructNave', 'value' => readRadioButton(78, 115, $Row) ),
                    array( 'key' => 'arq.SisEstructCrucero', 'value' => readRadioButton(116, 120, $Row) ),
                    // arq.SisEstructPresbiterio es una Checklist
                    array( 'key' => 'arq.SisEstructCoro', 'value' => readRadioButton(147, 164, $Row) ),
                    // arq.Pisos es una Checklist
                    // arq.Acabados es una Checklist
                    // arq.Materiales es una Checklist
                    array( 'key' => 'arq.GallinasCiegas', 'value' => readRadioButton(186, 190, $Row) ),
                    // arq.Bienes es una Checklist
                    array( 'key' => 'arq.ObservBienes', 'value' => $Row[199] ),
                    array( 'key' => 'arq.ObservGenerales', 'value' => $Row[200] ),
                    array( 'key' => 'arq.SearchString', 'value' =>
                        readRadioButton(17, 23, $Row).', '.$Row[0].', '.$Row[7].', '.$Row[8].', '.$Row[9]
                    )
                )
            );
            $jsonItem = json_encode($params['body']);
            //echo $jsonItem;

            $collectionID = chooseEntidad($Row[9]);
            uploadItem($jsonItem, $collectionID, $jsessionID);
        }
        //postChecklistData($jsessionID);
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

        $ids = getItems($jsessionID);
        $listOfItems = json_decode($ids,true);
        //var_dump($listOfItems);

        $i = 0;
        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            readChecklist(2, 6, $Row, "arq.Pertenencia", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(31, 36, $Row, "arq.Epoca", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(121, 146, $Row, "arq.SisEstructPresbiterio", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(165, 174, $Row, "arq.Pisos", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(175, 179, $Row, "arq.Acabados", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(180, 185, $Row, "arq.Materiales", $listOfItems[$i]['uuid'], $jsessionID );
            readChecklist(191, 198, $Row, "arq.Bienes", $listOfItems[$i]['uuid'], $jsessionID );
            $i++;
        }
    }

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