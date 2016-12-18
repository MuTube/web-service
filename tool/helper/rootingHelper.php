<?php

use Symfony\Component\Yaml\Yaml;

class RoutingHelper {
    public static function getCommandForPath($path) {
        $fullRootingData = Yaml::parse(file_get_contents('config/routing.yml'));
        $path = substr($path, 1);

        // some verifications
        if(substr($path, -1) == '/') $path = substr($path, 0, strlen($path) - 1);
        $pathSegments = explode("/", $path);

        if($path == "") {
            $redirectLocation = SessionController::checkSessionValidity() ? '/dashboard' : '/home';
            header("location:" . $redirectLocation);
        }

        foreach($fullRootingData as $item) {
            if($path == $item['url']) {
                return $item;
            }
            if(strpos($item['url'], ':') !== false) {
                if(self::pathMatchToRoute($pathSegments, $item['url'])) {
                    $params = self::getPathParams($pathSegments, $item['url']);
                    return array_merge($item, ['params' => $params]);
                }
            }
        }

        throw new FatalException('Error 404 :', "The requested page (/" . $path . ") doesn't exist...");
    }

    public static function validateCommandForUser($command) {
        $isLoggedIn = SessionController::checkSessionValidity();

        if(!$isLoggedIn && $command['loggedIn'] == 'true') {
            header('location:/home');
        }
    }

    protected static function pathMatchToRoute($pathSegments, $routePath) {
        $routePathSegments = explode("/", $routePath);

        if(count($pathSegments) != count($routePathSegments)) {
            return false;
        }

        foreach($pathSegments as $index => $pathSegment) {
            if(strpos($routePathSegments[$index], ':') === false) {
                if($routePathSegments[$index] != $pathSegment) {
                    return false;
                }
            }
        }

        return true;
    }

    protected static function getPathParams($pathSegments, $routePath) {
        $routePathSegments = explode("/", $routePath);
        $params = [];

        foreach($pathSegments as $index => $pathSegment) {
            if(strpos($routePathSegments[$index], ':') !== false) {
                $paramLabel = str_replace(":", "", $routePathSegments[$index]);
                $params[$paramLabel] = $pathSegment;
            }
        }

        return $params;
    }
}