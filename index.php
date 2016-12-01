<?php

include "controller/mainController.php";

//handle maintenance mode

session_start();

$mainController = new MainController();
$mainController->load();
