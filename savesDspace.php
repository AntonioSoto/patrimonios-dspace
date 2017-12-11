<?php

    include 'functions/dspaceFunctions.php';
    include 'functions/importItems.php';
    include 'functions/updateAllItems.php';
    include 'functions/importBitstreams.php';
    include 'functions/deleteItems.php';
    include 'functions/insertLatLngs.php';

    $output = "";

    /*if( isset($_REQUEST["btn_enviar"]) ){

        global $output;

        processUploadItem();

        $output = "Éxito.";
    }*/

    /*function processUploadItem(){

        // *** SUBIR METADATO ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        $currentDate = getdate(date("U"));
        $dspaceDate = "$currentDate[year]-$currentDate[mon]-$currentDate[mday]";

        $params['body'] = array(
            'metadata' => array(
                array( 'key' => 'dc.creator', 'value' => $_REQUEST["dcCreator"] ),
                array( 'key' => 'dc.title', 'value' => $_REQUEST["arqNombre"] ),
                array( 'key' => 'dc.date', 'value' => $dspaceDate ),
                array( 'key' => 'arq.Acabados', 'value' => $_REQUEST["arqAcabados"] ),
                array( 'key' => 'arq.Bienes', 'value' => $_REQUEST["arqBienes"] ),
                array( 'key' => 'arq.Catalogo', 'value' => $_REQUEST["arqCatalogo"] ),
                array( 'key' => 'arq.CategoriaActual', 'value' => $_REQUEST["arqCategoriaActual"] ),
                array( 'key' => 'arq.CategoriaOrigen', 'value' => $_REQUEST["arqCategoriaOrigen"] ),
                array( 'key' => 'arq.Entidad', 'value' => $_REQUEST["arqEntidad"] ),
                array( 'key' => 'arq.Localidad', 'value' => $_REQUEST["arqLocalidad"] ),
                array( 'key' => 'arq.Nombre', 'value' => $_REQUEST["arqNombre"] )
                //array( 'key' => 'arq.Ubicacion', 'value' =>  )
            )
        );

        $jsonItem = json_encode($params['body']);
        echo $jsonItem;

        $collectionID = "0a7e8fc5-2334-4a17-9ccc-9c9afb31f6c3";

        uploadItem($jsonItem, $collectionID, $jsessionID);
    }*/

    if( isset($_REQUEST["btn_subirFoto"]) ){

        global $output;

        processUploadPhoto();

        $output = "Éxito.";
    }

    if( isset($_REQUEST["btn_importItems"]) ){

        global $output;

        processImportItems();

        $output = "Éxito.";
    }

    if( isset($_REQUEST["btn_refreshItems"]) ){

        global $output;

        processRefreshItems();

        $output = "Éxito.";
    }

    if( isset($_REQUEST["btn_importBitstreams"]) ){

        global $output;

        processImportBitstreams();

        $output = "Éxito.";
    }

    if( isset($_REQUEST["btn_deleteItems"]) ){

        global $output;

        processDeleteItems();

        $output = "Éxito.";
    }

    if( isset($_REQUEST["btn_insertLatLngs"]) ){

        global $output;

        processInsertLatLngs();

        $output = "Éxito.";
    }

    function processUploadPhoto(){

        // *** SUBIR IMAGEN ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        $itemId = $_REQUEST["itemUUID"];
        $itemDescription = $_REQUEST["itemDescription"];

        uploadPhoto($itemId, $itemDescription, $jsessionID);
    }

    function processImportItems(){

        // *** IMPORTAR METADATOS DE EXCEL ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        importItems($jsessionID);
    }

    function processRefreshItems(){

        // *** ACTUALIZAR METADATOS DE EXCEL ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        updateAllItems($jsessionID);
    }

    function processImportBitstreams(){

        // *** IMPORTAR BITSTREAMS DE EXCEL ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        importBitstreams($jsessionID);
    }

    function processDeleteItems(){

        // *** ELIMINAR ITEMS ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        $ids = getIdsAndNames($jsessionID);

        deleteAllItems($ids, $jsessionID);
    }

    function processInsertLatLngs(){

        // *** INSERTAR LATITUD Y LONGITUD ***
        $jsessionID = getUserSessionID();
        loginToDspace();

        $ids = getIdsAndNames($jsessionID);

        searchLatLngs($ids, $jsessionID);
    }

    echo "<p>".$output."</p>";

?>