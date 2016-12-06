<?php

class Youtubeinmp3Client {
    public static function getMP3DownloadURLWithYoutubeID($youtubeID) {
        $url = "http://www.youtubeinmp3.com/fetch/?format=JSON&video=http://www.youtube.com/watch?v=" . $youtubeID;

        $json = CurlController::runGetRequest($url);
        $result = json_decode($json);

        return $result;
    }
}