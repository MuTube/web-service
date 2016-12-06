<?php

class CurlController {
    public static function runGetRequest($url) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}