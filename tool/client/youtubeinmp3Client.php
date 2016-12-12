<?php

class Youtubeinmp3Client {
    public static function getMP3DownloadURLWithYoutubeID($youtubeID) {
        $url = "http://www.youtubeinmp3.com/fetch/";

        $json = CurlController::runGetRequest($url, [
            'format' => 'JSON',
            'video' => 'http://www.youtube.com/watch?v=' . $youtubeID
        ]);

        $result = json_decode($json, true);

        if(isset($result['error'])) {
            throw new Exception("Youtube MP3 converter error : " . $result['error']);
        }

        return $result['link'];
    }
}