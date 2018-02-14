<?php

    include 'dspaceFunctions.php';

    $output = "";

    // *** ELIMINAR ITEMS ***
    $jsessionID = getUserSessionID();
    loginToDspace();
    $ids = getItems($jsessionID);
    deleteItems($ids, $jsessionID);

    $output = "Éxito.";

    function deleteItems($ids, $jsessionID){

        set_time_limit(0);

        $listOfItems = json_decode($ids,true);

        for($i=0; $i < count($listOfItems); $i++){

            deleteItem( $listOfItems[$i]['uuid'], $jsessionID );
        }
    }

    echo "<p>".$output."</p>";

?>