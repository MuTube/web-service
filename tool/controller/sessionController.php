<?php

class SessionController {

    public static function login($usrname, $pswd) {
        try {
            if(self::checkAuthentication($usrname, $pswd)) {
                $usrData = UserHelper::getDataForUsername($usrname);

                $_SESSION['user'] = [
                    'username' => $usrname,
                    'uid' => $usrData['id'],
                    'userData' => ['firstName' => $usrData['firstname'], 'lastName' => $usrData['lastname'], 'email' => $usrData['email']],
                    'userPermissions' => $usrData['permissions'],
                    'userLocation' => $usrData['userLocation']
                ];
            }
        } catch (Exception $e) {
            throw new SoftException($e->getMessage());
        }
    }

    public static function logout() {
        $_SESSION = [];

        if(ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    public static function checkSessionValidity() {
        if (!isset($_SESSION['user']['userData'])) {
            return false;
        }

        $userTable = DbController::getTable('user');
        $sessionData = [
            "id" => $_SESSION["user"]["uid"],
            "usrname" => $_SESSION["user"]["username"],
            "firstname" => $_SESSION["user"]["userData"]["firstName"],
            "lastname" => $_SESSION["user"]["userData"]["lastName"],
        ];

        return !empty($userTable->getForFullUserData($sessionData));
    }

    public static function checkAPIAuthentification($apiKey) {
        $userTable = DbController::getTable('user');

        if((!$userTable->getForAPIKey($apiKey) || $apiKey == "") && !self::checkSessionValidity()) {
            throw new Exception("Invalid API Key");
        }
    }

    public static function checkAuthentication($username, $pswd) {
        if($user = DbController::getTable('user')->getForUsername($username)) {
            if(hash_equals($user['pswd'], crypt($pswd, $user['pswd']))) {
                return true;
            }
            else {
                throw new Exception("Invalid password");
            }
        }
        else {
            throw new Exception("Invalid username and password");
        }
    }

    public static function passwordEncryption($pswd) {
        return crypt($pswd);
    }

    public static function generateAPIKey() {
        return sha1(microtime(true).mt_rand(10000,90000));
    }


}