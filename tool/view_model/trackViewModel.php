<?php

class TrackViewModel {
    // GET

    public static function getBy($by, $identifier, $returnNullIfNotFound = false) {
        if(($track = DbController::getTable('track')->getBy($by, $identifier)) != null) {
            return $track;
        }
        else {
            if ($returnNullIfNotFound) return null;
            throw new Exception("Track for " . $by . " '" . $identifier . "' not found.");
        }
    }

    public static function getList() {
        return DbController::getTable('track')->getList();
    }

    public static function getCount() {
        return DbController::getTable('track')->getCount();
    }

    public static function getOldest() {
        return DbController::getTable('track')->getOldest();
    }


    // ADD

    public static function addWithYoutubeId($youtubeId) {
        $newMP3FilePath = "files/track_files/" . $youtubeId . ".mp3";
        $mp3DownloadURL = Youtubeinmp3Client::getMP3DownloadURLWithYoutubeID($youtubeId);
        CurlController::downloadFileToDestination($mp3DownloadURL, $newMP3FilePath);

        if(($existingTrackHistoryData = TrackHistoryViewModel::getBy('youtube_id', $youtubeId, true)) == null) {
            $trackHistoryId = TrackHistoryViewModel::addWithYoutubeId($youtubeId);
        }
        else {
            $trackHistoryId = $existingTrackHistoryData['id'];
        }
        $newTrackData = [
            'track_history_id' => $trackHistoryId,
            'mp3_filepath' => $newMP3FilePath,
        ];

        self::validateData($newTrackData);

        if((TrackViewModel::getCount()["COUNT(*)"]) >= ConfigHelper::getMP3FileCachingConfig()['cache_size']) {
            $oldestTrackId = self::getOldest()['id'];
            self::removeBy('id', $oldestTrackId);
        }

        return DbController::getTable('track')->create($newTrackData);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(($track = DbController::getTable('track')->getBy($by, $identifier)) == null) {
            throw new Exception("Track for " . $by . " '" . $identifier . "' not found.");
        }

        FileManager::deleteFile($track['mp3_filepath']);
        DbController::getTable('track')->removeBy($by, $identifier);
    }


    // VALIDATION

    protected static function validateData($data) {
        if(array_key_exists('mp3_filepath', $data)) {
            if(!FileManager::fileExistWithPath($data['mp3_filepath'])) {
                throw new Exception('Username already exists');
            }
        }
    }
}