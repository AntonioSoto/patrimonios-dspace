<?php

    function deleteItems($ids, $jsessionID){

        $listOfItems = json_decode($ids,true);

        for($i=0; $i < count($listOfItems); $i++){

            deleteItem( $listOfItems[$i]['uuid'], $jsessionID );
        }
    }

    function deleteItem( $itemID, $jsessionID ){

        $url = "http://localhost:8080/rest/items/$itemID";

        $ch = curl_init($url);

        $cookieses = "JSESSIONID=".$jsessionID;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookieses);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $result = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200'){
            //throw $this->getException($ch);
            echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
            exit();
        }

        curl_close($ch);
    }

?>