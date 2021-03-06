<?php

class TrackCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        $this->data['tracks'] = TrackHistoryViewModel::getList();
        $this->setTemplate('track/list.html.twig');
    }

    public function read() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        try {
            $this->data['track'] = TrackHistoryViewModel::getBy('id', $this->params['path']['id']);
        }
        catch(Exception $e) {
            throw new FatalException('Cannot get the track :', $e->getMessage());
        }

        $this->setTemplate('track/read.html.twig');
    }

    public function remove() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);
        $ids = strpos($this->params['path']['id'], '-') ? explode('-', $this->params['path']['id']) : [$this->params['path']['id']];

        try {
            foreach($ids as $id) {
                TrackHistoryViewModel::removeBy('id', $id);
            }

            MessageController::addFlashMessage('success', "Tracks ".explode(', ', $ids)." successfully removed");
        }
        catch(Exception $e) {
            ExceptionHandler::renderFlashException($e);
        }

        $this->redirect('track');
    }

    public function edit() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        $trackHistoryData = TrackHistoryViewModel::getBy('id', $this->params['path']['id']);
        $form = new TrackFormHelper($trackHistoryData);

        $this->data['formValues'] = $form->getValues();
        $this->data['trackId'] = $trackHistoryData['id'];
        $this->data['track'] = $trackHistoryData;

        if(!empty($this->params['post'])) {
            try {
                $form->loadValues($this->params['post']);
                TrackHistoryViewModel::updateBy('id', $trackHistoryData['id'], $form->getValues());

                MessageController::addFlashMessage('success', 'Track "' . $form->getValues()['title'] . '" successfully updated');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
            }

            $this->redirect('track');
        }

        $this->setTemplate('track/edit.html.twig');
    }

    public function search() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        if(isset($this->params['get']['searchTerm'])) {
            $searchTerm = $this->params['get']['searchTerm'];
            $searchResults = YoutubeClient::getVideoSearchResultsForSearchTerm($searchTerm, TrackHistoryViewModel::getList());

            $this->data['searchTerm'] = $searchTerm;
            $this->data['searchResults'] = $searchResults;
        }

        $this->setTemplate('track/search.html.twig');
    }

    public function searchAutocomplete() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        if(isset($this->params['get']['searchTerm'])) {
            try {
                $searchTerm = $this->params['get']['searchTerm'];
                $result = YoutubeQuickDataClientClient::getYoutubeAutocompleteDataForSearchTerm($searchTerm);
                $this->renderJSON($result);
            } catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
                $this->redirect('track');
            }
        }
        else {
            $this->setNoContent();
        }
    }

    public function add() {
        $this->denyAccessWithoutOneOfPermissions(['track_management']);

        if(array_key_exists('yid', $this->params['get'])) {
            try {
                $this->data['track'] = YoutubeClient::getVideoDetailsForVideoId($this->params['get']['yid']);
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
                $this->redirect('track/add');
            }
        }
        elseif(array_key_exists('id', $this->params['post'])) {
            $form = new TrackFormHelper($this->params['post']);
            $this->data['formValues'] = $form->getValues();

            try {
                TrackHistoryViewModel::addWithYoutubeIdAndCustomData($this->params['post']['id'], $form->getValues());

                MessageController::addFlashMessage("success", "Track successfully saved");
                $this->redirect('track');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
                $this->redirect('track/add?yid='.$this->params['post']['id']);
            }
        }
        else {
            $this->redirect('add');
        }

        $this->setTemplate('track/add.html.twig');
    }

    public function downloadMP3() {
        $track = TrackViewModel::getBy('id', $this->params['path']['id']);
        $trackMP3Filepath = $track['mp3_filepath'];

        $this->forceDownload($trackMP3Filepath);
    }
}

//formHelper

class TrackFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['title', 'artist', 'album', "year"];
    }
}