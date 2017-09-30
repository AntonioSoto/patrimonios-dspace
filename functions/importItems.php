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
            //print_r($Row);
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
                    array( 'key' => 'arq.GoogleUbic', 'value' =>
                        'https://www.google.com.mx/maps/place/'.
                        urlencode(
                            readRadioButton(17, 23, $Row).' de '.
                            $Row[0].', '.$Row[7].', '.$Row[8].', '.$Row[9]
                        )
                    )
                )
            );
            $jsonItem = json_encode($params['body']);
            //echo $jsonItem;

            $collectionID = chooseEntidad($Row[9]);
            uploadItem($jsonItem, $collectionID, $jsessionID);
        }
        postChecklistData($jsessionID);
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

    function postChecklistData($jsessionID){

        $ids = getIdsAndNames($jsessionID);
        $listOfItems = json_decode($ids,true);
        //var_dump($listOfItems);

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            for($i=0; $i < count($listOfItems); $i++){

                if( $listOfItems[$i]["name"] == $Row[0] ){

                    readChecklist(2, 6, $Row, "arq.Pertenencia", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(31, 36, $Row, "arq.Epoca", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(121, 146, $Row, "arq.SisEstructPresbiterio", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(165, 174, $Row, "arq.Pisos", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(175, 179, $Row, "arq.Acabados", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(180, 185, $Row, "arq.Materiales", $listOfItems[$i]['uuid'], $jsessionID );
                    readChecklist(191, 198, $Row, "arq.Bienes", $listOfItems[$i]['uuid'], $jsessionID );
                }
            }
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

?>