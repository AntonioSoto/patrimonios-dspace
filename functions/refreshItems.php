<?php

$Columns = array();

function updateAllItems($jsessionID){

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
                array( 'key' => 'arq.TipologiaArqui', 'value' => readRadioButton(51, 72, $Row) ),
                array( 'key' => 'arq.Muros', 'value' => readRadioButton(73, 75, $Row) ),
                array( 'key' => 'arq.SistemaCubierta', 'value' => readRadioButton(76, 77, $Row) ),
                array( 'key' => 'arq.SisEstructNave', 'value' => readRadioButton(78, 115, $Row) ),
                array( 'key' => 'arq.SisEstructCrucero', 'value' => readRadioButton(116, 120, $Row) ),
                array( 'key' => 'arq.SisEstructPresbiterio', 'value' => readChecklist(121, 146, $Row) ),
                array( 'key' => 'arq.SisEstructCoro', 'value' => readRadioButton(147, 164, $Row) ),
                array( 'key' => 'arq.Pisos', 'value' => readChecklist(165, 174, $Row) ),
                array( 'key' => 'arq.Acabados', 'value' => readChecklist(175, 179, $Row) ),
                array( 'key' => 'arq.Materiales', 'value' => readChecklist(180, 185, $Row) ),
                array( 'key' => 'arq.GallinasCiegas', 'value' => readRadioButton(186, 190, $Row) ),
                array( 'key' => 'arq.Bienes', 'value' => readChecklist(191, 198, $Row) ),
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

        insertUpdateToItem( $jsonItem, $Row[0], $jsessionID );
    }
}

function insertUpdateToItem( $jsonItem, $dataName, $jsessionID ){

    $names = getIdsAndNames($jsessionID);
    $listOfNames = json_decode($names, true);

    for($i=0; $i < count($listOfNames); $i++){

        if( $listOfNames[$i]['name'] == $dataName ){

            updateItem($jsonItem, $listOfNames[$i]['uuid'], $jsessionID);
        }
    }
}

function updateItem($dspaceItem, $itemId, $jsessionID){

    $url = "http://localhost:8080/rest/items/$itemId/metadata";

    $ch = curl_init($url);

    $header = array(
        "Content-type: application/json",
        "Accept: application/json"
    );

    $cookieses = "JSESSIONID=".$jsessionID;

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dspaceItem);
    curl_setopt($ch, CURLOPT_COOKIE, $cookieses);

    $dspaceItem = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200'){
        echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
        exit();
    }

    curl_close($ch);
    return $dspaceItem;
}

?>