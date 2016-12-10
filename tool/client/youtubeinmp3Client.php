<?php

class Youtubeinmp3Client {
    public static function getMP3DownloadURLWithYoutubeID($youtubeID) {
        $url = "http://www.youtubeinmp3.com/fetch";

        $json = CurlController::runGetRequest($url, [
            'format' => 'JSON',
            'video' => 'http://www.youtube.com/watch?v=' . $youtubeID
        ]);

        $result = json_decode($json);

        return $result;
    }
}