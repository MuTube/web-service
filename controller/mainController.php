<?php

class MainController {
    //init
    function __construct() {
        require 'tool/helper/requireHelper.php';
        RequireHelper::requireAllRequiredFiles();

        $this->twig_controller = new TwigController('template');

        $this->uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->command = [];
        $this->data = ['params' => [], 'twig' => []];
        $this->output = ['template' => '', 'data' => []];
    }
    
    //properties
    private $twig_controller;

    private $uri;
    private $routingData;
    private $data;
    private $output;
    
    //actions
    public function load() {
        try {
            $this->prepare();
            $this->processCommand();
            $this->render();
        }
        catch(FatalException $e) {
            $outputData = ExceptionHandler::getRenderDataForFatalException($e);
            $this->renderException($outputData);
        }
    }

    private function prepare() {
        DbController::load();
        $this->routing();
        $this->getData();
    }

    private function processCommand() {
        require 'controller/' . $this->routingData['command'] . 'CommandController.php';
        $className = ucfirst(strtolower($this->routingData['command'])) . 'CommandController';
        $method = $this->routingData['method'] == 'default' ? '' : $this->routingData['method'];

        $controller = new $className($method, $this->data);
        $controller->load();

        $this->output = $controller->getOutput();
    }

    private function render() {
        $this->twig_controller->renderTemplateWithPath($this->output['template'], $this->output['data']);
    }

    private function renderException($renderData) {
        $this->twig_controller->renderTemplateWithPath($renderData['template'], $renderData['data']);
    }

    private function routing() {
        $this->routingData = RoutingHelper::getCommandForPath($this->uri);
        RoutingHelper::validateCommandForUser($this->routingData);
    }

    private function getData() {
        $this->data['params'] = DataHelper::getParams($this->routingData);
        $this->data['request'] = DataHelper::getRequestData();
        $this->data['user'] = DataHelper::getUserData();
        $this->data['twig'] = DataHelper::getTwigData($this->routingData);
    }
}