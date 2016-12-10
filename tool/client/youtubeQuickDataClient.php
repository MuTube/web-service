<?php

class YoutubeQuickDataClientClient {
    public static function getYoutubeAutocompleteDataForSearchTerm($searchTerm) {
        $url = "http://suggestqueries.google.com/complete/search";

        $json = CurlController::runGetRequest($url, [
            'client' => 'firefox',
            'ds' => 'yt',
            'q' => $searchTerm
        ]);

        $result = json_decode($json, true);

        return $result[1];
    }
}