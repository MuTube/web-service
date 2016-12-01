<?php

class HomeCommandController extends CommonCommandController {
    //actions
    public function defaultLoading() {
        $this->setTemplate('home/homepage.html.twig');
    }
}