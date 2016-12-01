<?php

class OpenweathermalClient {
    public static function getWeatherDataForLocation($location) {
        $location = str_replace(' ', '', $location);
        $apiKey = ConfigHelper::getOpenWheaterMapConfig()['api_key'];
        $url = "http://api.openweathermap.org/data/2.5/weather?q=$location&units=metric&cnt=7&lang=en&APPID=$apiKey";

        $json = CurlController::runGetRequest($url);
        $result = json_decode($json);

        return self::formatWeatherData($result);
    }

    protected static function formatWeatherData($weatherData) {
        return [
            'temp' => $weatherData->main->temp,
            'tempMin' => $weatherData->main->temp_min,
            'tempMax' => $weatherData->main->temp_max,
            'humidity' => $weatherData->main->humidity,
            'sunrise' => date('H:i'),
            'weather' => $weatherData->weather[0]->main.' - '.$weatherData->weather[0]->description,
            'iconUrl' => 'http://openweathermap.org/img/w/'.$weatherData->weather[0]->icon.'.png'
        ];
    }
}