<?php

class TrackViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('track')->getBy($by, $identifier);
    }

    public static function getList() {
        return DbController::getTable('track')->getList();
    }


    // ADD

    public static function addWithYoutubeId($youtubeId) {
        $newMP3FilePath = "files/track_files/" . $youtubeId . ".mp3";
        $mp3DownloadURL = Youtubeinmp3Client::getMP3DownloadURLWithYoutubeID($youtubeId);
        CurlController::downloadFileToDestination($mp3DownloadURL, $newMP3FilePath);

        if(($existingTrackHistoryData = TrackHistoryViewModel::getBy('youtube_id', $youtubeId)) == null) {
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
        return DbController::getTable('track')->create($newTrackData);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        $trackTable = DbController::getTable('track');
        $trackData = $trackTable->getBy($by, $identifier);

        FileManager::deleteFile($trackData['mp3_filepath']);
        $trackTable->removeBy($by, $identifier);
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