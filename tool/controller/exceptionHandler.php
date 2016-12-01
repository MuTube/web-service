<?php

class ExceptionHandler {
    public static function renderHardException($e) {
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

    public static function renderSoftException($e) {
        MessageController::addFlashMessage('error', $e->getOutputData()['message']);
    }
}