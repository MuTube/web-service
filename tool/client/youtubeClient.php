<?php

class YoutubeClient {
    public static function getVideoSearchResultsForSearchterm($searchTerm) {
        $apiKey = ConfigHelper::getYoutubeConfig()['api_key'];
        $url = "https://www.googleapis.com/youtube/v3/search?key=".$apiKey."&part=snippet&type=video&maxResults=50&q=".$searchTerm;

        $json = CurlController::runGetRequest($url);
        $result = json_decode($json);

        return self::formatYoutubeSearchResult($result);
    }

    public static function getVideoDetailsForVideoId($videoId) {
        $apiKey = ConfigHelper::getYoutubeConfig()['api_key'];
        $url = "https://www.googleapis.com/youtube/v3/videos?key=".$apiKey."&part=snippet,contentDetails,statistics&id=".$videoId;

        $json = CurlController::runGetRequest($url);
        $result = json_decode($json, true);

        return self::formatYoutubeVideoDetails($result);
    }

    protected static function formatYoutubeSearchResult($result) {
        $finalResult = [];

        foreach($result->items as $resultItem) {
            $finalResultItem = [
                "id" => $resultItem->id->videoId,
                "title" => $resultItem->snippet->title,
                "channel" => $resultItem->snippet->channelTitle,
                "thumbnailPath" => $resultItem->snippet->thumbnails->high->url,
                "publicationDate" => date_format(date_create($resultItem->snippet->publishedAt), 'Y-m-d')
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
        preg_match_all('/(\d+)/',$durationStr,$parts);

        // Put in zeros if we have less than 3 numbers.
        if (count($parts[0]) == 1) {
            array_unshift($parts[0], "0", "0");
        } elseif (count($parts[0]) == 2) {
            array_unshift($parts[0], "0");
        }

        $sec_init = $parts[0][2];
        $seconds = $sec_init%60;
        $seconds_overflow = floor($sec_init/60);

        $min_init = $parts[0][1] + $seconds_overflow;
        $minutes = ($min_init)%60;
        $minutes_overflow = floor(($min_init)/60);

        $hours = $parts[0][0] + $minutes_overflow;

        if($hours != 0) return $hours.':'.$minutes.':'.$seconds;
        else return $minutes.':'.$seconds;
    }
}