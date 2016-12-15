<?php

class RequireHelper {
    public static function requireAllRequiredFiles() {
        //composer components
        require 'vendor/autoload.php';

        //controller
        foreach(scandir('tool/controller') as $file) {
            if($file != '.' and $file != '..') {
                require 'tool/controller/' . $file;
            }
        }

        //helper
        foreach(scandir('tool/helper') as $file) {
            if($file != '.' and $file != '..' and $file != 'requireHelper.php') {
                require 'tool/helper/' . $file;
            }
        }

        //client
        foreach(scandir('tool/client') as $file) {
            if($file != '.' and $file != '..') {
                require 'tool/client/' . $file;
            }
        }

        //viewModel
        foreach(scandir('tool/view_model') as $file) {
            if($file != '.' and $file != '..') {
                require 'tool/view_model/' . $file;
            }
        }

        //dbHelper
        foreach(scandir('tool/db_helper') as $file) {
            if($file != '.' and $file != '..') {
                require 'tool/db_helper/' . $file;
            }
        }

        //exception
        foreach(scandir('tool/exception') as $file) {
            if($file != '.' and $file != '..') {
                require 'tool/exception/' . $file;
            }
        }

        //commandController
        require 'controller/commonCommandController.php';
    }
}