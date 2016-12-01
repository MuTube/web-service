<?php

class SoftException extends Exception {
    public function getOutputData() {
        return ['message' => $this->getMessage()];
    }
}