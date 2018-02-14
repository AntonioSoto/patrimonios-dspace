<?php

    include 'dspaceFunctions.php';

    $output = "";

    // *** IMPORTAR METADATOS DE EXCEL ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    importItems();

    $output = "Éxito.";

    $Columns = array();

    function importItems(){

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

            //$isItem = $Row[0] != "" && $Row[9] != "";
            //if( $isItem ){
                $params['body'] = array(
                    'metadata' => array(
                        array( 'key' => 'dc.creator', 'value' => $Row[9] ),
                        // array( 'key' => 'dc.creator', 'value' => $Row[7], $Row[8], $Row[9] ),
                        // array( 'key' => 'dc.creator', 'value' => $Row[7].', '.$Row[8].', '.$Row[9] ),
                        array( 'key' => 'dc.title', 'value' => $Row[0] ),
                        array( 'key' => 'dc.date', 'value' => $dspaceDate )
                    )
                );
                $jsonItem = json_encode($params['body']);
                //echo $jsonItem;
                $collectionID = chooseEntidad($Row[9]);
                global $jsessionID;
                uploadItem($jsonItem, $collectionID, $jsessionID);
            //}
        }
        postArqData();
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

    function postArqData(){

        global $jsessionID;
        $ids = getItems($jsessionID);
        $listOfItems = json_decode($ids,true);
        //var_dump($listOfItems);

        $i = 0;
        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            //$isItem = $Row[0] != "" && $Row[9] != "";
            //if( $isItem ){
                $uuid = $listOfItems[$i]['uuid'];
                postSingleData( "arq.Nombre", $Row[0], $uuid );
                postSingleData( "arq.Direccion", $Row[1], $uuid );
                postRepeatedData( "arq.Pertenencia", 2, 6, $Row, $uuid );
                postSingleData( "arq.Localidad", $Row[7], $uuid );
                postSingleData( "arq.Municipio", $Row[8], $uuid );
                postSingleData( "arq.Entidad", $Row[9], $uuid );
                postSingleData( "arq.Jurisdiccion", readRadioButton(10, 16, $Row), $uuid );
                postSingleData( "arq.CategoriaActual", readRadioButton(17, 23, $Row), $uuid );
                postSingleData( "arq.CategoriaOriginal", readRadioButton(24, 30, $Row), $uuid );
                postRepeatedData( "arq.Epoca", 31, 36, $Row, $uuid );
                postSingleData( "arq.Anio", $Row[37], $uuid );
                postSingleData( "arq.UsoActual", readRadioButton(38, 40, $Row), $uuid );
                postSingleData( "arq.Fundador", $Row[41], $uuid );
                postSingleData( "arq.Constructor", $Row[42], $uuid );
                postSingleData( "arq.EstadoConservacion", readRadioButton(43, 45, $Row), $uuid );
                postSingleData( "arq.Tipologia", readRadioButton(46, 50, $Row), $uuid );
                postSingleData( "arq.TipologiaArqui", readRadioButton(51, 72, $Row), $uuid );
                postSingleData( "arq.Muros", readRadioButton(73, 75, $Row), $uuid );
                postSingleData( "arq.SistemaCubierta", readRadioButton(76, 77, $Row), $uuid );
                postSingleData( "arq.SisEstructNave", readRadioButton(78, 115, $Row), $uuid );
                postSingleData( "arq.SisEstructCrucero", readRadioButton(116, 120, $Row), $uuid );
                postRepeatedData( "arq.SisEstructPresbiterio", 121, 146, $Row, $uuid );
                postSingleData( "arq.SisEstructCoro", readRadioButton(147, 164, $Row), $uuid );
                postRepeatedData( "arq.Pisos", 165, 174, $Row, $uuid );
                postRepeatedData( "arq.Acabados", 175, 179, $Row, $uuid );
                postRepeatedData( "arq.Materiales", 180, 185, $Row, $uuid );
                postSingleData( "arq.GallinasCiegas", readRadioButton(186, 190, $Row), $uuid );
                postRepeatedData( "arq.Bienes", 191, 198, $Row, $uuid );
                postSingleData( "arq.ObservBienes", $Row[199], $uuid );
                postSingleData( "arq.ObservGenerales", $Row[200], $uuid );
                postSingleData( "arq.SearchString",
                    readRadioButton(17, 23, $Row).', '.$Row[0].', '.$Row[7].', '.$Row[8].', '.$Row[9],
                    $uuid
                );
            //}
            $i++;
        }
    }

    function postSingleData( $key, $value, $uuid ){

        if( $value != "" ){
            $metadata = array( array( 'key' => $key, 'value' => $value ) );
            $jsonItem = json_encode($metadata);
            global $jsessionID;
            postMetadataToItem( $jsonItem, $uuid, $jsessionID );
        }
    }

    function postRepeatedData($key, $columnMin, $columnMax, $Row, $uuid){

        global $Columns;
        $metadata = "";

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){

                $metadata = array( array( 'key' => $key, 'value' => $Columns[$i] ) );
                $jsonItem = json_encode($metadata);
                global $jsessionID;
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