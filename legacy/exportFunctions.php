<?php

    // Código no utilizado.

    function loginToDspace(){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/rest/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "email=jose44_sp@hotmail.com&password=dsp.1_ace");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $agent = "curl/7.53.1";
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200'){

            echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
            exit();
        }

        curl_close ($ch);
    }

    function getUserSessionID(){

        $ch = curl_init('http://localhost:8080/rest/status');
        curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $userResults = curl_exec ($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $userResults, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        $jsessionID = $cookies['JSESSIONID'];

        curl_close ($ch);
        return $jsessionID;
    }

    function getIdsAndNames($jsessionID){

        $url = "http://localhost:8080/rest/items/?limit=200";

        $ch = curl_init($url);

        $cookieses = "JSESSIONID=".$jsessionID;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookieses);

        $result = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != '200'){

            echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
            exit();
        }

        curl_close($ch);
        return $result;
    }
?>