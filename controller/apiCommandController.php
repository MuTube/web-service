<?php


/**
 * Version 1 of the Mutube API
 *
 * @SWG\Info(
 *     title="Commands",
 *     version="1.0",
 * )
 *  @SWG\Swagger(
 *     basePath="/api/run",
 *     produces={"application/json"}
 * )
 */
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

    public function swaggerJson() {
        $swagger = \Swagger\scan('controller/apiCommandController.php');
        header('Content-Type: application/json');
        echo $swagger;
        exit();
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

    /**
     * @SWG\Get(
     *   path="/track/request/{youtube_id}",
     *   summary="Request a MP3 download for a given youtube id",
     *   tags={"Track"},
     *   @SWG\Parameter(name="youtube_id", type="string", required=true, in="path"),
     *   @SWG\Response(response=200,description="Youtube MP3 download url"),
     *   @SWG\Response(response=500,description="There was an error during the download, get error details")
     * )
     */
    protected function RequestTrack() {
        $youtubeId = $this->resourceId;

        if(!isset($youtubeId) || $youtubeId == "") {
            throw new Exception("Invalid youtube id");
        }

        if(($existingTrackData = TrackViewModel::getBy('mp3_filepath', "files/track_files/" . $youtubeId . ".mp3")) == null) {
            $trackId = TrackViewModel::addWithYoutubeId($youtubeId);
        }
        else {
            $trackId = $existingTrackData['id'];
        }

        return($this->data['baseUrl'] . "/track/downloadMp3/" . $trackId);
    }

    /**
     * @SWG\Get(
     *   path="/trackData/get/{youtube_id}",
     *   summary="Get detail of a specific registered youtube id",
     *   tags={"Track data"},
     *   @SWG\Parameter(name="youtube_id", type="string", required=true, in="path"),
     *   @SWG\Response(response=200,description="Youtube track data"),
     *   @SWG\Response(response=404,description="Youtube id not found in DB")
     * )
     */
    protected function GetTrackData() {
        $youtubeId = $this->resourceId;

        if(!isset($youtubeId) || $youtubeId == "") {
            throw new Exception("Invalid youtube id");
        }

        $trackData = TrackHistoryViewModel::getBy('youtube_id', $youtubeId);

        if($trackData == null) {
            throw new Exception("Track with youtube id '" . $youtubeId . "' is not registered at the moment");
        }

        unset($trackData['id']);
        unset($trackData['thumbnail_filepath']);
        unset($trackData['duration']);
        unset($trackData['youtube_channel']);
        unset($trackData['youtube_views']);

        return $trackData;
    }

    /**
     * @SWG\Post(
     *   path="/trackData/push/{youtube_id}",
     *   summary="Push track data for a given youtube id",
     *   tags={"Track data"},
     *   @SWG\Parameter(name="youtube_id", type="string", required=true, in="path"),
     *   @SWG\Parameter(name="title", type="string", required=false, in="formData"),
     *   @SWG\Parameter(name="artist", type="string", required=false, in="formData"),
     *   @SWG\Parameter(name="album", type="string", required=false, in="formData"),
     *   @SWG\Parameter(name="year", type="string", required=false, in="formData"),
     *   @SWG\Response(response=200,description="Get push confirmation"),
     *   @SWG\Response(response=404,description="Youtube id not found on Youtube")
     * )
     */
    protected function PushTrackData() {
        $youtubeId = $this->resourceId;

        if(!isset($youtubeId) || $youtubeId == "") {
            throw new Exception("Invalid youtube id");
        }

        if(!isset($this->additionalParams)) {
            throw new Exception("No track data");
        }

        if(($existingTrackHistoryData = TrackHistoryViewModel::getBy('youtube_id', $youtubeId)) == null) {
            $trackHistoryId = TrackHistoryViewModel::addWithYoutubeId($youtubeId);
        }
        else {
            TrackHistoryViewModel::updateBy('youtube_id', $youtubeId, $this->additionalParams);
            $trackHistoryId = $existingTrackHistoryData['id'];
        }

        return "Track data with id " . $trackHistoryId . " successfully pushed";
    }
}