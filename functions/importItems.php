<?php

    $Columns = array();

    function importItems($jsessionID){

        $currentDate = getdate(date("U"));
        $dspaceDate = "$currentDate[year]-$currentDate[mon]-$currentDate[mday]";

        require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
        require('spreadsheet-reader-master/SpreadsheetReader_XLSX.php');

        $Reader = new SpreadsheetReader_XLSX("Columns.xlsx");
        global $Columns;
        foreach ($Reader as $Row){
            $Columns = $Row;
        }
        //print_r($Columns);

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {
            //print_r($Row);
            $params['body'] = array(
                'metadata' => array(
                    array( 'key' => 'dc.creator', 'value' => 'User' ),
                    array( 'key' => 'dc.title', 'value' => $Row[0] ),
                    array( 'key' => 'dc.date', 'value' => $dspaceDate ),
                    array( 'key' => 'arq.Nombre', 'value' => $Row[0] ),
                    array( 'key' => 'arq.Direccion', 'value' => $Row[1] ),
                    array( 'key' => 'arq.Pertenencia', 'value' => readChecklist(2, 6, $Row) ),
                    array( 'key' => 'arq.Localidad', 'value' => $Row[7] ),
                    array( 'key' => 'arq.Municipio', 'value' => $Row[8] ),
                    array( 'key' => 'arq.Entidad', 'value' => $Row[9] ),
                    array( 'key' => 'arq.Jurisdiccion', 'value' => readRadioButton(10, 16, $Row) ),
                    array( 'key' => 'arq.CategoriaActual', 'value' => readRadioButton(17, 23, $Row) ),
                    array( 'key' => 'arq.CategoriaOriginal', 'value' => readRadioButton(24, 30, $Row) ),
                    array( 'key' => 'arq.Epoca', 'value' => readChecklist(31, 36, $Row) ),
                    array( 'key' => 'arq.Anio', 'value' => $Row[37] ),
                    array( 'key' => 'arq.UsoActual', 'value' => readRadioButton(38, 40, $Row) ),
                    array( 'key' => 'arq.Fundador', 'value' => $Row[41] ),
                    array( 'key' => 'arq.Constructor', 'value' => $Row[42] ),
                    array( 'key' => 'arq.EstadoConservacion', 'value' => readRadioButton(43, 45, $Row) ),
                    array( 'key' => 'arq.Tipologia', 'value' => readRadioButton(46, 50, $Row) ),
                    array( 'key' => 'arq.TipologiaArqui', 'value' => readRadioButton(51, 54, $Row) ),
                    array( 'key' => 'arq.Muros', 'value' => readRadioButton(55, 57, $Row) ),
                    array( 'key' => 'arq.SistemaCubierta', 'value' => readRadioButton(58, 59, $Row) ),
                    array( 'key' => 'arq.SisEstructNave', 'value' => readRadioButton(60, 96, $Row) ),
                    array( 'key' => 'arq.SisEstructCrucero', 'value' => readRadioButton(97, 101, $Row) ),
                    array( 'key' => 'arq.SisEstructPresbiterio', 'value' => readChecklist(102, 124, $Row) ),
                    array( 'key' => 'arq.SisEstructCoro', 'value' => readRadioButton(123, 142, $Row) ),
                    array( 'key' => 'arq.Pisos', 'value' => readChecklist(143, 152, $Row) ),
                    array( 'key' => 'arq.Acabados', 'value' => readChecklist(153, 157, $Row) ),
                    array( 'key' => 'arq.Materiales', 'value' => readChecklist(158, 163, $Row) ),
                    array( 'key' => 'arq.GallinasCiegas', 'value' => readRadioButton(164, 168, $Row) ),
                    array( 'key' => 'arq.Bienes', 'value' => readChecklist(169, 176, $Row) ),
                    array( 'key' => 'arq.BienesObserv', 'value' => $Row[177] ),
                    array( 'key' => 'arq.GoogleUbic', 'value' =>
                        'https://www.google.com.mx/maps/place/'
                        .urlencode(readRadioButton(17, 23, $Row)).' de '.urlencode($Row[0]).', '
                        .urlencode($Row[7]).', '.urlencode($Row[8]).', '.urlencode($Row[9]).
                        '/'
                    )
                )
            );
            $jsonItem = json_encode($params['body']);
            //echo $jsonItem;
            $collectionID = chooseEntidad($Row[9]);
            uploadItem($jsonItem, $collectionID, $jsessionID);
        }
    }

    function chooseEntidad($entidad){
        switch ($entidad){
            case "Yucatán":
                return "0a7e8fc5-2334-4a17-9ccc-9c9afb31f6c3";
                break;
            case "Campeche":
                return "691688c9-0e76-4a85-901a-1f7b4d45f37f";
                break;
            case "Quintana Roo":
                return "2d1e2449-f748-4127-92db-91b908b106ea";
                break;
            default:
                break;
        }
        return "";
    }

    function readChecklist($columnMin, $columnMax, $Row){

        global $Columns;
        $output = "";

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){
                if( $output != "" ){
                    $output .= ", ";
                }
                $output .= $Columns[$i];
            }
        }

        return $output;
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

?>