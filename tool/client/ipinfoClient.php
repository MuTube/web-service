<?php

class IpinfoClient {
    public static function getUserLocation() {
        $json = CurlController::runGetRequest('http://ipinfo.io/'.UserHelper::getCurrentUserIp().'/org');
        $locationDetail = json_decode($json);

        return isset($locationDetail->city) ? $locationDetail : false;
    }
}