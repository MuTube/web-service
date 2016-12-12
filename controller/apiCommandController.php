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
        $this->resource = $this->params['path']['ressource'];
        $this->resourceId = $this->params['path']['id'];
        $this->action = $this->params['path']['action'];

        try {
            $apiKey = isset($this->params["get"]["key"]) ? $this->params["get"]["key"] : "";
            SessionController::checkAPIAuthentification($apiKey);
            $method = $this->action . ucfirst($this->resource);

            if (!method_exists($this, $method)) {
                throw new Exception("Method '$method' doesn't exist...");
            }

            $result = ["success" => true, "data" => $this->$method()];
        } catch (Exception $e) {
            $result = ['success' => false, 'error' => $e->getMessage()];
        }

        $this->renderJSON($result);
    }

    protected function getAdditionalParams() {
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

            // HANDLE ERROR AND RETRY IF ITS NOT A FATAL ERROR
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

}