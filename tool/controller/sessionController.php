<?php

class SessionController {

    public static function login($username, $password) {
        if(self::checkAuthentication($username, $password)) {
            $userData = UserHelper::getDataForCurrentUserOrUsername($username);

            $ok = false;
            foreach($userData['permissions'] as $userPermission) {
                if($userPermission['name'] == 'user_login') {
                    $ok = true;
                }
            }
            if(!$ok) throw new Exception('This user has not the permission to login into the interface');

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
        if(!function_exists('hash_equals')) {
            function hash_equals($str1, $str2) {
                if(strlen($str1) != strlen($str2)) {
                    return false;
                } else {
                    $res = $str1 ^ $str2;
                    $ret = 0;
                    for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
                    return !$ret;
                }
            }
        }

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