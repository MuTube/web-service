<?php

class ApiCommandController extends CommonCommandController {

    protected $httpMethod;
    protected $resource;
    protected $action;
    protected $resourceId;
    protected $additionalParams;

    public function defaultLoading() {
        // HANDLE API DOCUMENTATION

        $this->setTemplate('api/doc.html.twig');
    }

    public function run() {
        $this->httpMethod = $this->request['method'];
        $this->resource = $this->params['path']['ressource'];
        $this->resourceId = $this->params['path']['id'];
        $this->action = $this->params['path']['action'];

        try {
            $apiKey = isset($this->params["get"]["key"]) ? $this->params["get"]["key"] : "";
            SessionController::checkAPIAuthentification($apiKey);
            $method = $this->action . ucfirst($this->resource);
            $this->setAdditionalParams();

            if (!method_exists($this, $method)) {
                throw new Exception("Method '$method' doesn't exist...");
            }

            $result = ["success" => true, "data" => $this->$method()];
        } catch (Exception $e) {
            $result = ['success' => false, 'error' => $e->getMessage()];
        }

        $this->renderJSON($result);
    }

    protected function setAdditionalParams() {
        $this->additionalParams = [];

        if ($this->httpMethod == 'GET'){
            $this->additionalParams = $this->params['get'];
        }
        if ($this->httpMethod == 'POST'){
            $this->additionalParams = $this->params['post'];
        }
        if ($this->httpMethod == 'PUT'){
            $this->additionalParams = $this->params['post'];
        }
    }

    // API Methods

    protected function RequestTrack() {
        $youtubeId = $this->resourceId;

        if(!isset($youtubeId) || $youtubeId == "") {
            throw new Exception("Invalid youtube id");
        }

        $trackHistoryTable = DbController::getTable('trackHistory');
        $trackTable = DbController::getTable('track');
        $newMP3FilePath = "files/track_files/" . $youtubeId . ".mp3";

        if(($existingTrackData = $trackTable->getByMP3FilePath($newMP3FilePath)) == null && !FileManager::fileExistWithPath($newMP3FilePath)) {
            $mp3DownloadURL = Youtubeinmp3Client::getMP3DownloadURLWithYoutubeID($youtubeId);
            CurlController::downloadFileToDestination($mp3DownloadURL, $newMP3FilePath);

            if(($existingTrackHistoryData = $trackHistoryTable->getByYoutubeId($youtubeId)) == null) {
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
                $trackHistoryId = $trackHistoryTable->create($newTrackHistoryData);
            }
            else {
                $trackHistoryId = $existingTrackHistoryData['id'];
            }


            $newTrackData = [
                'track_history_id' => $trackHistoryId,
                'mp3_filepath' => $newMP3FilePath,
            ];

            $trackId = $trackTable->create($newTrackData);
        }
        else {
            $trackId = $existingTrackData['id'];
        }

        return($this->data['baseUrl'] . "/track/downloadMp3/" . $trackId);
    }

    protected function GetTrackData() {
        $trackHistoryId = $this->resourceId;

        if(!isset($trackHistoryId) || $trackHistoryId == "") {
            throw new Exception("Invalid id");
        }

        $trackData = DbController::getTable('trackHistory')->getById($trackHistoryId);

        if($trackData == null) {
            throw new Exception("Invalid id");
        }

        unset($trackData['id']);
        unset($trackData['thumbnail_filepath']);
        unset($trackData['duration']);
        unset($trackData['youtube_channel']);
        unset($trackData['youtube_views']);

        return $trackData;
    }

    protected function PushTrackData() {
        $youtubeId = $this->resourceId;

        if(!isset($youtubeId) || $youtubeId == "") {
            throw new Exception("Invalid youtube id");
        }

        if(!isset($this->additionalParams)) {
            throw new Exception("No track data");
        }

        $trackHistoryTable = DbController::getTable('trackHistory');

        if(($existingTrackHistoryData = $trackHistoryTable->getByYoutubeId($youtubeId)) == null) {
            if(($youtubeTrackData = YoutubeClient::getVideoDetailsForVideoId($youtubeId)) == null) {
                throw new Exception("Invalid youtube id");
            }

            $newThumbnailFilePath = "files/track_thumbnails/".$youtubeId."_thumbnail.png";
            $newTrackHistoryData = [
                'title' => isset($this->additionalParams['title']) ? $this->additionalParams['title'] : $youtubeTrackData['title'],
                'artist' => isset($this->additionalParams['artist']) ? $this->additionalParams['artist'] : "",
                'album' =>isset($this->additionalParams['album']) ? $this->additionalParams['album'] : "",
                'year' => isset($this->additionalParams['year']) ? $this->additionalParams['year'] : "",
                'youtube_channel' => $youtubeTrackData['youtube_channel'],
                'youtube_views' => $youtubeTrackData['youtube_views'],
                'duration' => $youtubeTrackData['duration'],
                'youtube_id' => $youtubeTrackData['id'],
                'thumbnail_filepath' => $newThumbnailFilePath
            ];

            CurlController::downloadFileToDestination($youtubeTrackData['thumbnailPath'], $newThumbnailFilePath);
            $trackHistoryId = $trackHistoryTable->create($newTrackHistoryData);
        }
        else {
            $trackHistoryId = $existingTrackHistoryData['id'];
            $trackHistoryTable->updateById($trackHistoryId, $this->additionalParams);
        }

        return "Track data with id " . $trackHistoryId . " successfully pushed";
    }
}