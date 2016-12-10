<?php

class CurlController {
    public static function runGetRequest($url, $params = []) {
        if(count($params) != 0) $url = self::buildGetURL($url, $params);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 2);

        $result = curl_exec($ch);

        if($error = curl_error($ch) != "") {
            throw new Exception("CURL Error : " . $error);
        }

        curl_close($ch);

        return $result;
    }

    public static function downloadFileToDestination($url, $destinationPath) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);

        $result = curl_exec($ch);

        if($error = curl_error($ch) != "") {
            throw new Exception("CURL Error : " . $error);
        }

        curl_close ($ch);

        FileManager::processCurlDownloadedImageToDestination($result, $destinationPath);
    }

    protected static function buildGetURL($url, $params) {
        $resultUrl = $url . "?";

        foreach($params as $label => $param) {
            $resultUrl .= $label . "=" . urlencode($param) . "&";
        }

        return rtrim($resultUrl, "&");
    }
}