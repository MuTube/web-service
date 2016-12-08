<?php

class TrackCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        $trackTable = DbController::getTable('trackHistory');
        $this->data['tracks'] = $trackTable->getList();

        $this->setTemplate('track/list.html.twig');
    }

    public function read() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        $userTable = DbController::getTable('trackHistory');
        $this->data['track'] = $userTable->getById($this->params['path']['id']);

        $this->setTemplate('track/read.html.twig');
    }

    public function remove() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);
        $ids = strpos($this->params['path']['id'], '-') ? explode('-', $this->params['path']['id']) : [$this->params['path']['id']];

        try {
            DbController::getTable('trackHistory')->removeWithIds($ids);
            DbController::getTable('track')->removeWithTrackHistoryId($ids);

            // HANDLE FILE AND THUMBNAIL DELETION

            MessageController::addFlashMessage('success', "Tracks ".explode(', ', $ids)." successfully removed");
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('track');
    }

    public function edit() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        $trackTable = DbController::getTable('track_history');
        $track = $trackTable->getById($this->params['path']['id']);
        $form = new TrackFormHelper($track);

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                $trackTable->updateById($track['id'], $form->getValues());

                MessageController::addFlashMessage('success', 'Track "' . $form->getValues()['usrname'] . '" successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
            }

            $this->redirect('track/' . $track['id'] . '/edit');
        }

        $this->data['formValues'] = $form->getValues();
        $this->data['trackId'] = $track['id'];
        $this->data['userImageName'] = $track['image_name'];

        $this->setTemplate('track/edit.html.twig');
    }

    public function add() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        if(isset($this->params['get']['searchTerm'])) {
            $searchTerm = $this->params['get']['searchTerm'];

            $this->data['searchTerm'] = $searchTerm;
            $this->data['searchResults'] = YoutubeClient::getVideoSearchResultsForSearchterm($searchTerm);
        }

        $this->setTemplate('track/add.html.twig');
    }

    public function register() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);


        if(array_key_exists('yid', $this->params['get'])) {
            try {
                $this->data['videoData'] = YoutubeClient::getVideoDetailsForVideoId($this->params['get']['yid']);
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
                $this->redirect('track/add');
            }
        }
        elseif(array_key_exists('id', $this->params['post'])) {
            $form = new TrackFormHelper($this->params['post']);
            $trackTable = DbController::getTable('trackHistory');

            try {
                $trackData = YoutubeClient::getVideoDetailsForVideoId($this->params['post']['id']);
                $newThumbnailFilePath = "files/track_thumbnails/".$trackData['id']."_thumbnail.png";
                $newTrack = array_merge($form->getValues(), [
                    'youtube_channel' => $trackData['youtube_channel'],
                    'youtube_views' => $trackData['youtube_views'],
                    'duration' => $trackData['duration'],
                    'youtube_id' => $trackData['id'],
                    'thumbnail_filepath' => $newThumbnailFilePath
                ]);

                CurlController::downloadFileToDestination($trackData['thumbnailPath'], $newThumbnailFilePath);
                $trackTable->create($newTrack);

                MessageController::addFlashMessage("success", "Track successfully saved");
                $this->redirect('track');
            }
            catch(Exception $e) {
                ExceptionHandler::renderSoftException($e);
                $this->redirect('track/register/yid='.$this->params['post']['id']);
            }
        }

        $this->setTemplate('track/register.html.twig');
    }
}

//formHelper

class TrackFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['title', 'artist', 'album', "year"];
    }
}