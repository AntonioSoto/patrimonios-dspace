<?php

    function importBitstreams($jsessionID){

        require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
        require('spreadsheet-reader-master/SpreadsheetReader_XLSX.php');

        $Reader = new SpreadsheetReader_XLSX($_FILES["excelFile"]["tmp_name"]);
        foreach ($Reader as $Row) {

            $rowlength = (count($Row) - 2) / 2;
            $filename = 0;
            $filepath = 1;
            for($i = 0; $i < $rowlength; $i++){

                $filename = $filename + 2;
                $filepath = $filepath + 2;
                uploadPhotoExcel($Row[0],$Row[1],$Row[$filename],$Row[$filepath],$jsessionID);
            }
        }
    }

    function uploadPhotoExcel($itemId, $itemDescription, $filename, $filepath, $jsessionID){

        $description = urlencode($itemDescription);

        $url = "http://localhost:8080/rest/items/$itemId/bitstreams?name=$filename&description=$description";

        $ch = curl_init($url);

        $cookieses = "JSESSIONID=".$jsessionID;
        $headers = array("Content-Type: text/plain", "Accept: application/json");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filepath));

        curl_setopt($ch, CURLOPT_COOKIE, $cookieses);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $result = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200'){

            echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
            exit();
        }

        $headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT );
        echo "<p>".$headerSent."</p>";

        curl_close($ch);
        return $result;
    }

?>