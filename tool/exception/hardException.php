<?php

class HardException extends Exception {
    function __construct($title, $message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->title = $title;
    }

    protected $title;

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