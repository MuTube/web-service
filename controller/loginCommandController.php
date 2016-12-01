<?php

class LoginCommandController extends CommonCommandController {
    //actions
    public function defaultLoading() {
        if($this->user['valid']) $this->redirect('dashboard');

        if(isset($this->params['post']['usrname']) && isset($this->params['post']['pswd'])) {
            try {
                SessionController::login($this->params['post']['usrname'], $this->params['post']['pswd']);
                $this->redirect('dashboard');
            }
            catch(SoftException $e) {
                ExceptionHandler::renderSoftException($e);
                $this->redirect('admin-login');
            }
        }

        $this->setTemplate('login/form.html.twig');
    }
}