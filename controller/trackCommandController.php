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
            // HANDLE FILE DELETION
            MessageController::addFlashMessage('success', "Tracks ".explode(', ', $ids)." successfully removed");
        }
        catch(Exception $e) {
            ExceptionHandler::renderSoftException($e);
        }

        $this->redirect('user');
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
}

//formHelper

class TrackFormHelper extends FormHelper {
    protected function defineFields() {
        $this->fields = ['name', 'artist', 'album'];
    }
}