<?php

class TrackHistoryViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('trackHistory')->getBy($by, $identifier);
    }

    public static function getList() {
        return DbController::getTable('trackHistory')->getList();
    }

    // ADD

    public static function addWithYoutubeId($youtubeId) {
        if(($youtubeTrackData = YoutubeClient::getVideoDetailsForVideoId($youtubeId)) == null) {
            throw new Exception("Invalid youtube id");
        }

        $trackHistoryData = YoutubeClient::getVideoDetailsForVideoId($youtubeId);
        $newThumbnailFilePath = "files/track_thumbnails/".$youtubeId."_thumbnail.png";
        $newTrackHistoryData = [
            'title' => $trackHistoryData['title'],
            'artist' => '',
            'album' => '',
            'year' => '',
            'youtube_channel' => $trackHistoryData['youtube_channel'],
            'youtube_views' => $trackHistoryData['youtube_views'],
            'duration' => $trackHistoryData['duration'],
            'youtube_id' => $trackHistoryData['id'],
            'thumbnail_filepath' => $newThumbnailFilePath
        ];

        CurlController::downloadFileToDestination($trackHistoryData['thumbnailPath'], $newThumbnailFilePath);
        self::validateData($newTrackHistoryData);
        return DbController::getTable('trackHistory')->create($newTrackHistoryData);
    }

    public static function addWithYoutubeIdAndCustomData($youtubeId, $customData) {
        if(($youtubeTrackData = YoutubeClient::getVideoDetailsForVideoId($youtubeId)) == null) {
            throw new Exception("Invalid youtube id");
        }

        if(empty($customData)) {
            throw new Exception("No data provided");
        }

        $trackData = YoutubeClient::getVideoDetailsForVideoId($youtubeId);
        $newThumbnailFilePath = "files/track_thumbnails/".$trackData['id']."_thumbnail.png";
        $newTrackHistoryData = array_merge([
            'youtube_channel' => $trackData['youtube_channel'],
            'youtube_views' => $trackData['youtube_views'],
            'duration' => $trackData['duration'],
            'youtube_id' => $trackData['id'],
            'thumbnail_filepath' => $newThumbnailFilePath
        ], $customData);

        CurlController::downloadFileToDestination($trackData['thumbnailPath'], $newThumbnailFilePath);
        self::validateData($newTrackHistoryData);
        DbController::getTable('trackHistory')->create($newTrackHistoryData);

    }

    // UPDATE

    public static function updateBy($by, $identifier, $values) {
        if(empty($identifier)) {
            throw new Exception("No data provided");
        }

        self::validateData($values);
        DbController::getTable('trackHistory')->updateBy($by, $identifier, $values);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        $trackHistoryTable = DbController::getTable('trackHistory');
        $trackHistoryData = $trackHistoryTable->getBy($by, $identifier);

        FileManager::deleteFile($trackHistoryData['thumbnail_filepath']);
        $trackHistoryTable->removeBy($by, $identifier);

        if(($trackData = TrackViewModel::getBy('track_history_id', $trackHistoryData['id'])) != null) {
            TrackViewModel::removeBy('id', $trackData['id']);
        }
    }


    // VALIDATION

    protected static function validateData($data) {
        if(array_key_exists('thumbnail_filepath', $data)) {
            if(!FileManager::fileExistWithPath($data['thumbnail_filepath'])) {
                throw new Exception("File given doesn't exists");
            }
        }

        if(array_key_exists('year', $data) && $data['year'] != '') {
            if(!is_numeric($data['year']) || strlen($data['year']) != 4) {
                throw new Exception("The year should be a numeric value and should contain 4 numbers");
            }
        }
    }
}