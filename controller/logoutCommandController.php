<?php

class LogoutCommandController extends CommonCommandController {
    //actions
    public function defaultLoading() {
        if($this->user['valid']) {
            SessionController::logout();
        }

        $this->redirect('admin-login');
    }
}