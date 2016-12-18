<?php

class ExceptionHandler {
    public static function getRenderDataForFatalException($e) {
        $data = $e->getOutputData();
        $traces = [];

        foreach($data['trace'] as $trace) {
            array_push($traces, $trace['file'] . ' on line ' . $trace['line'] . ' (' . $trace['class'] . $trace['type'] . $trace['function'] . ')');
        }

        $data = [
            'title' => $data['title'],
            'message' => $data['message'],
            'throwIn' => $data['file'] . ' on line ' . $data['line'],
            'traces' => $traces
        ];

        return ['template' => 'exception/read.html.twig', 'data' => $data];
    }

    public static function renderFlashException($e) {
        MessageController::addFlashMessage('error', $e->getMessage());
    }
}

class FatalException extends Exception {
    protected $title;

    function __construct($title, $message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->title = $title;
    }

    public function getOutputData() {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTrace()
        ];
    }

    final public function getTitle() {
        return $this->title;
    }
}