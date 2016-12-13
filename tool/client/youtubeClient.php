<?php

class YoutubeClient {
    public static function getVideoSearchResultsForSearchTerm($searchTerm, $registredTracks) {
        $apiKey = ConfigHelper::getYoutubeConfig()['api_key'];
        $url = "https://www.googleapis.com/youtube/v3/search";

        $json = CurlController::runGetRequest($url, [
            'key' => $apiKey,
            'part' => 'snippet',
            'type' => 'video',
            'maxResults' => '50',
            'q' => $searchTerm
        ]);

        $result = json_decode($json, true);
        $result = self::unsetAlreadyRegisteredSearchResults($result, $registredTracks);

        return self::formatYoutubeSearchResult($result);
    }

    public static function getVideoDetailsForVideoId($videoId) {
        $apiKey = ConfigHelper::getYoutubeConfig()['api_key'];
        $url = "https://www.googleapis.com/youtube/v3/videos";

        $json = CurlController::runGetRequest($url, [
            'key' => $apiKey,
            'part' => 'snippet,contentDetails,statistics',
            'id' => $videoId
        ]);

        $result = json_decode($json, true);

        if(count($result['items']) != 0) {
            return self::formatYoutubeVideoDetails($result);
        }
        else {
            return null;
        }
    }

    protected static function formatYoutubeSearchResult($result) {
        $finalResult = [];

        foreach($result['items'] as $resultItem) {
            $finalResultItem = [
                "id" => $resultItem['id']['videoId'],
                "title" => $resultItem['snippet']['title'],
                "channel" => $resultItem['snippet']['channelTitle'],
                "thumbnailPath" => $resultItem['snippet']['thumbnails']['high']['url'],
                "publicationDate" => date_format(date_create($resultItem['snippet']['publishedAt']), 'Y-m-d')
            ];

            array_push($finalResult, $finalResultItem);
        }

        return $finalResult;
    }

    protected static function formatYoutubeVideoDetails($result) {
        return [
            "id" => $result['items'][0]['id'],
            "title" => $result['items'][0]['snippet']['title'],
            "duration" => self::formatYoutubeVideoDuration($result['items'][0]['contentDetails']['duration']),
            "thumbnailPath" => $result['items'][0]['snippet']['thumbnails']['high']['url'],
            "youtube_channel" => $result['items'][0]['snippet']['channelTitle'],
            "youtube_views" => $result['items'][0]['statistics']['viewCount']
        ];
    }

    protected static function formatYoutubeVideoDuration($durationStr) {
        $result = str_replace("PT", "", $durationStr);
        $result = str_replace("M", ":", $result);
        $result = str_replace("S", "", $result);

        if(strpos($result, ':') == 1) {
            $result = '0' . $result;
        }

        if(strlen(substr($result, strpos($result, ':'))) == 2) {
            $seconds = substr($result, -1);
            $result = rtrim($result, $seconds);
            $result .= '0' . $seconds;
        }

        return $result;
    }

    protected static function unsetAlreadyRegisteredSearchResults($results, $alreadyDownloadedTracks) {
        foreach($alreadyDownloadedTracks as $alreadyDownloadedTrack) {
            foreach($results['items'] as $resultIndex => $result) {
                if($alreadyDownloadedTrack['youtube_id'] == $result['id']['videoId']) {
                    unset($results['items'][$resultIndex]);
                }
            }
        }

        return $results;
    }
}