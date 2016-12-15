<?php

class UserViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('user')->getBy($by, $identifier);
    }

    public static function getList() {
        return DbController::getTable('user')->getList();
    }

    public static function getForFullData($data) {
        return DbController::getTable('user')->getForFullUserData($data);
    }

    public static function getPermissionsBy($by, $identifier) {
        return DbController::getTable('user')->getPermissionsBy($by, $identifier);
    }

    // ADD

    public static function add($data, $image = false) {
        if(UserViewModel::getBy('usrname', $data['usrname']) != null) {
            throw new Exception('Username already exists');
        }

        $userTable = DbController::getTable('user');
        $id = $userTable->create($data);
        $userTable->updatePasswordForUid($id, $data['pswd']);
        $userTable->updateAPIKeyForUid($id, SessionController::generateAPIKey());

        if($image != false) {
            $newFileName = $id . '_' . $image['name'];

            FileManager::processUserImage($image, null, $newFileName);
            $userTable->updateById($id, ['image_name' => $newFileName]);
        }

        return $id;
    }


    // UPDATE

    public static function updateBy($by, $identifier, $data, $image = false) {
        $user = UserViewModel::getBy($by, $identifier);
        $userTable = DbController::getTable('user');
        $userTable->updateBy($by, $identifier, $data);

        if($image != false) {
            $newFileName = $user['id'] . '_' . $image['name'];
            $oldFileName = empty($user['image_name']) ? $newFileName : $user['image_name'];

            FileManager::processUserImage($image, $oldFileName, $newFileName);
            $userTable->updateById($user['id'], ['image_name' => $newFileName]);
        }
    }

    public static function updatePasswordBy($by, $identifier, $values) {
        $userTable = DbController::getTable('user');

        $userTable->validatePasswordReset($values);
        $userTable->updatePasswordBy($by, $identifier, $values['newPassword']);
    }

    public static function resetAPIKeyBy($by, $identifier) {
        $userTable = DbController::getTable('user');
        $userTable->updateAPIKeyBy($by, $identifier, SessionController::generateAPIKey());
    }

    // REMOVE

    public static function removeBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        $userTable = DbController::getTable('user');
        $userData = $userTable->getBy($by, $identifier);

        FileManager::deleteFile("files/user_image/" . $userData['image_name']);
        $userTable->removeBy($by, $identifier);
    }

    public static function removeRoleFromUserWithRoleId($roleId) {

    }
}