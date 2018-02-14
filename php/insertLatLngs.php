<?php

    include 'dspaceFunctions.php';

    $output = "";

    // *** INSERTAR LATITUD Y LONGITUD ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    $ids = getItems($jsessionID);
    searchLatLngs($ids, $jsessionID);

    $output = "Éxito.";

    function searchLatLngs($ids, $jsessionID){

        set_time_limit(0);
        set_error_handler('exceptions_error_handler');

        $listOfItems = json_decode($ids,true);

        for($i=0; $i < count($listOfItems); $i++){

            $listOfMetadatas = json_decode( getItemMetadata($listOfItems[$i]['uuid'], $jsessionID), true );
            $catAct='';
            $nombre='';
            $localidad='';
            $municipio='';
            $entidad='';
            for($j=0; $j < count($listOfMetadatas); $j++){

                if( $listOfMetadatas[$j]['key'] == 'arq.CategoriaActual' ){
                    $catAct = $listOfMetadatas[$j]['value'];
                }
                if( $listOfMetadatas[$j]['key'] == 'arq.Nombre' ){
                    $nombre = $listOfMetadatas[$j]['value'];
                }
                if( $listOfMetadatas[$j]['key'] == 'arq.Localidad' ){
                    $localidad = $listOfMetadatas[$j]['value'];
                }
                if( $listOfMetadatas[$j]['key'] == 'arq.Municipio' ){
                    $municipio = $listOfMetadatas[$j]['value'];
                }
                if( $listOfMetadatas[$j]['key'] == 'arq.Entidad' ){
                    $entidad = $listOfMetadatas[$j]['value'];
                }
            }

            if($catAct!='' && $nombre!='' && $localidad!='' && $municipio!='' && $entidad!=''){
                echo $address = $catAct.'+'.$nombre.'+'.$localidad.'+'.$municipio.'+'.$entidad;
                insertLatLngs( urlencode($address), $listOfItems[$i]['uuid'], $jsessionID );
            }
        }
    }

    function insertLatLngs($address, $uuid, $jsessionID){

        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        try {
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response);
            echo $lat = $response_a->results[0]->geometry->location->lat;
            echo "<br />";
            echo $long = $response_a->results[0]->geometry->location->lng;

            $metadata = array( array( 'key' => 'arq.Latitud', 'value' => $lat ) );
            $jsonItem = json_encode($metadata);
            postMetadataToItem( $jsonItem, $uuid, $jsessionID );

            $metadata = array( array( 'key' => 'arq.Longitud', 'value' => $long ) );
            $jsonItem = json_encode($metadata);
            postMetadataToItem( $jsonItem, $uuid, $jsessionID );

        } catch (Exception $e) {
            echo '--error--';
        }
    }

    function exceptions_error_handler($severity, $message, $filename, $lineno) {
        if (error_reporting() == 0) {
            return;
        }
        if (error_reporting() & $severity) {
            throw new ErrorException($message, 0, $severity, $filename, $lineno);
        }
    }

    echo "<p>".$output."</p>";

?>