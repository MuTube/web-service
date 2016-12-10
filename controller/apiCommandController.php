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
}