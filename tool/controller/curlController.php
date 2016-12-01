<?php

class CurlController {
    public static function runGetRequest($url) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true]);

        return curl_exec($curl);
    }

    public static function runGetRequestWithAuth($url, $username, $password) {
        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_RETURNTRANSFER => true
        ]);

        return curl_exec($curl);
    }
}