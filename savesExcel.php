<?php

    include 'functions/exportFunctions.php';

    if( isset($_REQUEST["btn_excel"]) ){

        processDataToExcel();
    }

    function cleanData(&$str){

        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", "\\n", $str);
        if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

    function dataToExcel($data){

        $filename = "website_data_" . date('Ymd') . ".xls";

        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach($data as $row) {
            if(!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, __NAMESPACE__ . '\cleanData');
            echo implode("\t", array_values($row)) . "\r\n";
        }
    }

    function processDataToExcel(){

        $jsessionID = getUserSessionID();
        loginToDspace();

        $listOfItems = json_decode(getIdsAndNames($jsessionID),true);
        //var_dump($listOfItems);

        $data = array();
        for($i=0; $i < count($listOfItems); $i++){

            $row = array();
            $row["UUID"] = $listOfItems[$i]['uuid'];
            $row["Name"] = $listOfItems[$i]['name'];

            $data[$i] = $row;
        }

        //var_dump($data);

        dataToExcel($data);
    }

?>