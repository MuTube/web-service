<?php

class DataHelper {
    public static function getParams($routingData) {
        $params['get'] = $_GET;
        $params['post'] = $_POST;
        $params['files'] = $_FILES;
        $params['path'] = isset($routingData['params']) ? $routingData['params'] : [];

        $params = self::santizeParams($params);

        return $params;
    }

    public static function getTwigData($routingData) {
        $siteInfo = ConfigHelper::getSiteInformations();

        $data['siteInfo'] = [
            'name' => $siteInfo['site_name'],
            'author' => $siteInfo['site_author'],
            'year' => $siteInfo['site_year']
        ];

        $data['baseUrl'] = 'http://' . $_SERVER['SERVER_NAME'];
        $data['currentDate'] = date('d.m.Y');
        $data['isValidUser'] = SessionController::checkSessionValidity();
        $data['userPermissions'] = $data['isValidUser'] ? $_SESSION['user']['userPermissions'] : [];
        $data['userData'] = $data['isValidUser'] ? ['uid' => $_SESSION['user']['uid'], 'username' => $_SESSION['user']['username']] : [];
        $data['flashMessage'] = isset($_SESSION['flashMessage']) ? $_SESSION['flashMessage'] : [];
        $data['activeMenuLink'] = explode('/', $routingData['url'])[0];
        $_SESSION['flashMessage'] = [];

        return $data;
    }

    public static function getUserData() {
        $user = isset($_SESSION['user']) ? $_SESSION['user'] : [];
        $user['valid'] = SessionController::checkSessionValidity();

        return $user;
    }

    public static function getRequestData() {
        $request = [];

        $request['uri'] = $_SERVER['REQUEST_URI'];
        $request['method'] = $_SERVER['REQUEST_METHOD'];

        return $request;
    }

    protected static function santizeParams($params) {
        foreach($params as $paramType => $filtredParams) {
            foreach($filtredParams as $label => $param) {
                if(!is_array($param)) {
                    $param = self::sanitizeParam($param, $paramType);
                    $params[$paramType][$label] = $param;
                } else {
                    foreach($param as $subParamLabel => $subParam) {
                        $subParam = self::sanitizeParam($subParam, $paramType);
                        $params[$paramType][$label][$subParamLabel] = $subParam;
                    }
                }
            }
        }

        return $params;
    }

    protected static function sanitizeParam($param, $paramType) {
        $param = filter_var($param, FILTER_SANITIZE_STRING);
        $param = addslashes($param);

        if($paramType == 'get') {
            $param = urldecode($param);
        }

        return $param;
    }
}