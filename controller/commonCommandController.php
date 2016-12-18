<?php

class CommonCommandController {
    //init
    function __construct($command, $data) {
        $this->class = strtolower(str_replace('CommandController', '', get_class($this)));
        $this->command = $command;
        $this->user = $data['user'];
        $this->params = $data['params'];
        $this->data = $data['twig'];
        $this->request = $data['request'];
        $this->template = $this->class . '/' . ($this->command != '' ? $this->command : 'default') . '.html.twig';
    }

    //properties
    protected $class;
    protected $command;
    protected $user;
    protected $params = [];
    protected $data = [];
    protected $template = [];
    protected $request = [];
    
    //actions
    public function load() {
        $this->commandRouting();
    }

    public function getOutput() {
        return [
            'template' => $this->template,
            'data' => $this->data
        ];
    }

    protected function commandRouting() {
        if($this->command) {
            $commandName = $this->command;
            $this->$commandName();
        }
        else {
            $this->defaultLoading();
        }
    }
    
    protected function redirect($uri) {
        Header('Location:/' . $uri);
        exit();
    }

    protected function forceDownload($fileName) {
        header("Content-Disposition: attachment; filename=\"" . basename($fileName) . "\"");
        header("Content-Type: application/force-download");
        header("Content-Length: " . filesize($fileName));
        header("Connection: close");
        readfile($fileName);

        exit();
    }

    protected function setTemplate($template) {
        $this->template = $template;
    }

    protected function isAjaxCall() {
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
        else return false;
    }

    protected function setNoContent() {
        exit();
    }

    protected function renderAjax($output) {
        echo $output;
        exit();
    }

    protected function renderJSON($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function denyAccessWithoutOneOfPermissions($permissions) {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $isAllowed = false;

        foreach($permissions as $permission) {
            foreach(UserHelper::getPermissionsForCurrentUserOrUid() as $userPermission) {
                if($userPermission['name'] == $permission) $isAllowed = true;
            }
        }

        if(!$isAllowed) throw new FatalException('Access Denied', 'You are not allowed to see this page. If you reallly need the access right, please contact the webmaster...');
    }

    // to define in each commandController
    public function defaultLoading() {}
}