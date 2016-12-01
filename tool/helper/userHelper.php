<?php

class UserHelper {

    public static function getDataForUsername($usrname) {
        $userTable = DbController::getTable('user');

        $userData = $userTable->getForUsername($usrname);
        $userData['permissions'] = UserHelper::getPermissionsForCurrentUserOrUid($userData['id']);
        $userData['userLocation'] = IpinfoClient::getUserLocation();

        return $userData;
    }

    public static function getPermissionsForCurrentUserOrUid($uid = false) {
        $uid = !$uid ? $_SESSION['user']['uid'] : $uid;

        $staticPermissions = DbController::getTable('user')->getPermissionsForUid($uid);
        $dynamicPermissions = self::getDynamicPermissionsForUid($uid);

        return array_merge($staticPermissions, $dynamicPermissions);
    }

    public static function getCurrentUserLocation() {
        $location = isset($_SESSION['user']['userLocation']) ? $_SESSION['user']['userLocation'] : false;
        $output = !empty($location->city) ? $location->city.', ' : '';
        $output .= !empty($location->region) ? $location->region.', ' : '';
        $output .= !empty($location->country) ? $location->country : '';

        return $output;
    }

    //https://www.chriswiegman.com/2014/05/getting-correct-ip-address-php/
    public static function getCurrentUserIp() {
        if ( function_exists( 'apache_request_headers' ) ) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
        ) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {

            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }
        return $the_ip;
    }

    protected static function getDynamicPermissionsForUid($uid) {
        $dynamicPermissions = [];

        //user
        $userDynamicPermissions = ['user_'.$uid.'_edit', 'user_'.$uid.'_change_password', 'user_'.$uid.'_read', 'user_'.$uid.'_reset_api_key'];
        foreach($userDynamicPermissions as $userDynamicPermission) {
            $data = explode('_', $userDynamicPermission);
            array_push($dynamicPermissions, [
                'name' => $userDynamicPermission,
                'description' => 'Allow to '.$data[2].' '.$data[0].' '.$data[1]
            ]);
        }

        return $dynamicPermissions;
    }
}