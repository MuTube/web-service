<?php

class SessionController {

    public static function login($username, $password) {
        try {
            if(self::checkAuthentication($username, $password)) {
                $userData = UserHelper::getDataForCurrentUserOrUsername($username);

                $_SESSION['user'] = [
                    'username' => $username,
                    'uid' => $userData['id'],
                    'userData' => [
                        'firstName' => $userData['firstname'],
                        'lastName' => $userData['lastname'],
                        'email' => $userData['email']
                    ],
                    'userPermissions' => $userData['permissions'],
                    'userLocation' => $userData['userLocation']
                ];
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
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

        $sessionData = [
            "id" => $_SESSION["user"]["uid"],
            "username" => $_SESSION["user"]["username"],
            "firstname" => $_SESSION["user"]["userData"]["firstName"],
            "lastname" => $_SESSION["user"]["userData"]["lastName"],
        ];

        return !empty(UserViewModel::getByMultipleValues($sessionData));
    }

    public static function checkAPIAuthentification($apiKey) {
        if(($apiKey == "" || !UserViewModel::getBy('api_key', $apiKey)) && !self::checkSessionValidity()) {
            throw new Exception("Invalid API Key");
        }
    }

    public static function checkAuthentication($username, $password) {
        if($user = UserViewModel::getBy('username', $username)) {
            if(hash_equals($user['password'], crypt($password, $user['password']))) {
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

    public static function passwordEncryption($password) {
        return crypt($password);
    }

    public static function generateAPIKey() {
        return sha1(microtime(true).mt_rand(10000,90000));
    }


}