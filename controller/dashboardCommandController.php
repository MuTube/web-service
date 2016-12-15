<?php

class DashboardCommandController extends CommonCommandController {
    public function defaultLoading() {
        $this->data['blocks'] = $this->getBlocks();
        $this->setTemplate('dashboard/dashboard.html.twig');
    }

    protected function getBlocks() {
        $blocksName = ['userBlock'];
        $blocks = [];

        foreach($blocksName as $blockName) {
            $methodName = 'get' . ucfirst($blockName) . 'Data';
            array_push($blocks, ['name' => $blockName, 'data' => $this->$methodName()]);
        }

        return $blocks;
    }

    protected function getUserBlockData() {
        $userLoc = UserHelper::getCurrentUserLocation();
        $weatherData = substr_count($userLoc, ',') == 2
            ? OpenweathermalClient::getWeatherDataForLocation(substr($userLoc, strpos($userLoc, ',')+2))
            : false;

        return [
            'user' => UserViewModel::getBy('id', $this->user['uid']),
            'userLocation' => $userLoc,
            'weatherData' => $weatherData,
        ];
    }
}