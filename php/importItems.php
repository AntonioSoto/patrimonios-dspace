<?php

    include 'dspaceFunctions.php';

    $output = "";
    $Columns = array();
    $params["body"] = array();

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
        global $Columns, $params;
        foreach ($Reader as $Row){
            //print_r($Row);
            $Columns = $Row;
        }
        //print_r($Columns);

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {
            //print_r($Row);
            // Un item de dspace debe tener como mínimo: creador, título y fecha.
            $isItem = $Row[9] != "" && $Row[0] != "" && $dspaceDate != "";
            if( $isItem ){

                $params['body'] = array( 'metadata' => array() );
                addRegularData( 'dc.creator', $Row[9] );
                addRegularData( 'dc.title', $Row[0] );
                addRegularData( 'dc.date', $dspaceDate );
                addRegularData( 'arq.Nombre', $Row[0] );
                addRegularData( 'arq.Direccion', $Row[1] );
                addRepeatedData( 'arq.Pertenencia', 2, 6, $Row );
                addRegularData( 'arq.Localidad', $Row[7] );
                addRegularData( 'arq.Municipio', $Row[8] );
                addRegularData( 'arq.Entidad', $Row[9] );
                addSingleData( 'arq.Jurisdiccion', 10, 16, $Row );
                addSingleData( 'arq.CategoriaActual', 17, 23, $Row );
                addSingleData( 'arq.CategoriaOriginal', 24, 30, $Row );
                addRepeatedData( 'arq.Epoca', 31, 36, $Row );
                addRegularData( 'arq.Anio', $Row[37] );
                addSingleData( 'arq.UsoActual', 38, 40, $Row );
                addRegularData( 'arq.Fundador', $Row[41] );
                addRegularData( 'arq.Constructor', $Row[42] );
                addSingleData( 'arq.EstadoConservacion', 43, 45, $Row );
                addSingleData( 'arq.Tipologia', 46, 50, $Row );
                addSingleData( 'arq.TipologiaArqui', 51, 72, $Row );
                addSingleData( 'arq.Muros', 73, 75, $Row );
                addSingleData( 'arq.SistemaCubierta', 76, 77, $Row );
                addSingleData( 'arq.SisEstructNave', 78, 115, $Row );
                addSingleData( 'arq.SisEstructCrucero', 116, 120, $Row );
                addRepeatedData( 'arq.SisEstructPresbiterio', 121, 146, $Row );
                addSingleData( 'arq.SisEstructCoro', 147, 164, $Row );
                addRepeatedData( 'arq.Pisos', 165, 174, $Row );
                addRepeatedData( 'arq.Acabados', 175, 179, $Row );
                addRepeatedData( 'arq.Materiales', 180, 185, $Row );
                addSingleData( 'arq.GallinasCiegas', 186, 190, $Row );
                addRepeatedData( 'arq.Bienes', 191, 198, $Row );
                addRegularData( 'arq.ObservBienes', $Row[199] );
                addRegularData( 'arq.ObservGenerales', $Row[200] );
                addRegularData( 'arq.SearchString',
                    getSingleData(17, 23, $Row).', '.$Row[0].', '.$Row[7].', '.$Row[8].', '.$Row[9]
                );
                //print_r($params["body"]["metadata"]);

                $jsonItem = json_encode($params['body']);
                //echo $jsonItem;
                $collectionID = chooseEntidad($Row[9]);
                uploadItem($jsonItem, $collectionID, $jsessionID);
            }
        }
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

    // Del Excel, son las celdas que son strings.
    function addRegularData( $key, $data ){

        global $params;

        if( $data != "" ){

            $metadata = array( 'key' => $key, 'value' => $data );
            array_push($params['body']['metadata'], $metadata);
        }
    }

    // Del Excel, son las celdas marcadas con "x" que se pueden repetir.
    function addRepeatedData( $key, $columnMin, $columnMax, $Row ){

        global $Columns, $params;

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){

                $metadata = array( 'key' => $key, 'value' => $Columns[$i] );
                array_push($params['body']['metadata'], $metadata);
            }
        }
    }

    // Del Excel, son las celdas marcadas con "x" que NO se pueden repetir.
    function addSingleData( $key, $columnMin, $columnMax, $Row ){

        global $Columns, $params;

        for( $i = $columnMin; $i <= $columnMax; $i++ ){

            if( $Row[$i] != "" ){

                $metadata = array( 'key' => $key, 'value' => $Columns[$i] );
                array_push($params['body']['metadata'], $metadata);
                break;
            }
        }
    }

    function getSingleData($columnMin, $columnMax, $Row){

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