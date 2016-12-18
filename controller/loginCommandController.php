<?php

class LoginCommandController extends CommonCommandController {
    //actions
    public function defaultLoading() {
        if($this->user['valid']) $this->redirect('dashboard');

        if(isset($this->params['post']['username']) && isset($this->params['post']['password'])) {
            try {
                SessionController::login($this->params['post']['username'], $this->params['post']['password']);
                $this->redirect('dashboard');
            }
            catch(Exception $e) {
                ExceptionHandler::renderFlashException($e);
                $this->redirect('admin-login');
            }
        }

        $this->setTemplate('login/login.html.twig');
    }
}