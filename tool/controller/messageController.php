<?php

class MessageController {
    protected static $messageTypes = ['success', 'error'];

    public static function addFlashMessage($type, $message) {
        if(!isset($_SESSION['flashMessage'][$type])) $_SESSION['flashMessage'][$type] = [];

        foreach (self::$messageTypes as $messageType) {
            if($type == $messageType) {
                array_push($_SESSION['flashMessage'][$type], $message);
            }
        }

        if(!isset($_SESSION['flashMessage'][$type])) throw new HardException("flashMessage Error :", "flashMessage invalid type");
    }
}