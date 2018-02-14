<?php

    include 'dspaceFunctions.php';

    $output = "";
    $Columns = array();

    // *** ACTUALIZAR METADATOS DE EXCEL ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    updateItems($jsessionID);

    $output = "Éxito.";

    function updateItems($jsessionID){

        $currentDate = getdate(date("U"));
        $dspaceDate = "$currentDate[year]-$currentDate[mon]-$currentDate[mday]";

        $ids = getItems($jsessionID);
        $listOfItems = json_decode($ids,true);
        $i=0;

        /*$listOfMetadatas[] = null;
        for($i=0; $i < count($listOfItems); $i++){

            $metadatas = json_decode(getItemMetadata($listOfItems[$i]['uuid'], $jsessionID), true);
            $listOfMetadatas[$i] = $metadatas;
        }*/

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
                    array( 'key' => 'arq.GoogleUbic', 'value' =>
                        'https://www.google.com.mx/maps/place/'.
                        urlencode(
                            readRadioButton(17, 23, $Row).', '.$Row[0].', '.$Row[7].', '.$Row[8].', '.$Row[9]
                        )
                    )
                )
            );
            $jsonItem = json_encode($params['body']);
            //echo $jsonItem;

            /*for($i=0; $i < count($listOfMetadatas); $i++){

                for($j=0; $j < count($listOfMetadatas[$i]); $j++){
                    //print_r($listOfMetadatas[$i][$j]);
                    if( $listOfMetadatas[$i][$j]['key'] == 'arq.Nombre' ){
                        if($listOfMetadatas[$i][$j]['value'] == $Row[0]){

                        }
                    }
                }
            }*/
            updateItem($jsonItem, $listOfItems[$i]['uuid'], $jsessionID);
            $i++;
        }
    }

    /*function insertUpdateToItem( $jsonItem, $dataName, $jsessionID ){

        $names = getIdsAndNames($jsessionID);
        $listOfNames = json_decode($names, true);

        for($i=0; $i < count($listOfNames); $i++){

            if( $listOfNames[$i]['name'] == $dataName ){

                updateItem($jsonItem, $listOfNames[$i]['uuid'], $jsessionID);
            }
        }
    }*/

    echo "<p>".$output."</p>";

?>