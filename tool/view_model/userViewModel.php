<?php

class UserViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(($user = DbController::getTable('user')->getBy($by, $identifier)) != null) {
            return $user;
        }
        else {
            throw new Exception("User for " . $by . " '" . $identifier . "' not found.");
        }
    }

    public static function getList() {
        return DbController::getTable('user')->getList();
    }

    public static function getByMultipleValues($data) {
        if(($user = DbController::getTable('user')->getByMultipleValues($data)) != null) {
            return $user;
        }
        else {
            throw new Exception("User for not found for given data");
        }
    }

    public static function getPermissionsBy($by, $identifier) {
        if(($permissions = DbController::getTable('user')->getPermissionsBy($by, $identifier)) != null) {
            return $permissions;
        }
        else {
            throw new Exception("User for " . $by . " '" . $identifier . "' not found.");
        }
    }

    // ADD

    public static function add($data, $passwordData, $image = false) {
        self::validateData($data);

        $userTable = DbController::getTable('user');
        $id = $userTable->create($data);
        self::updatePasswordBy('id', $id, $passwordData);
        self::resetAPIKeyBy('id', $id);

        if($image != false) {
            $newFileName = $id . '_' . $image['name'];

            FileManager::processUserImage($image, null, $newFileName);
            $userTable->updateBy('id', $id, ['image_filepath' => 'files/user_image/' . $newFileName]);
        }

        return $id;
    }


    // UPDATE

    public static function updateBy($by, $identifier, $data, $image = false) {
        self::validateData($data);

        if(($user = $user = UserViewModel::getBy($by, $identifier)) == null) {
            throw new Exception("User for " . $by . " '" . $identifier . "' not found.");
        }

        $userTable = DbController::getTable('user');
        $userTable->updateBy($by, $identifier, $data);

        if($image != false) {
            $newFileName = $user['id'] . '_' . $image['name'];
            $oldFileName = empty($user['image_name']) ? $newFileName : $user['image_name'];

            FileManager::processUserImage($image, $oldFileName, $newFileName);
            $userTable->updateBy('id', $user['id'], ['image_filepath' => 'files/user_image/' . $newFileName]);
        }
    }

    public static function updatePasswordBy($by, $identifier, $values) {
        if(($user = $user = UserViewModel::getBy($by, $identifier)) == null) {
            throw new Exception("User for " . $by . " '" . $identifier . "' not found.");
        }

        self::validatePasswordReset($values['password'], $values['password_confirmation']);
        DbController::getTable('user')->updatePasswordBy($by, $identifier, $values['password']);
    }

    public static function resetAPIKeyBy($by, $identifier) {
        $userTable = DbController::getTable('user');
        $userTable->updateAPIKeyBy($by, $identifier, SessionController::generateAPIKey());
    }

    // REMOVE

    public static function removeBy($by, $identifier) {
        if(($user = $user = UserViewModel::getBy($by, $identifier)) == null) {
            throw new Exception("User for " . $by . " '" . $identifier . "' not found.");
        }

        FileManager::deleteFile("files/user_image/" . $user['image_filepath']);
        DbController::getTable('user')->removeBy($by, $identifier);
    }

    public static function removeRoleFromUsersWithRoleId($roleId) {
        DbController::getTable('user')->removeRoleFromUsers($roleId);
    }


    // VALIDATION

    protected static function validateData($data) {
        if(array_key_exists('email', $data)) {
            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL) && !empty($data['email'])) {
                throw new Exception('Invalid email adress');
            }
        }

        if(array_key_exists('role_id', $data)) {
            if(empty($data['role_id'])) {
                throw new Exception('A role is required');
            }
        }
    }

    protected static function validatePasswordReset($newPassword, $newPasswordConfirmation) {
        if($newPassword != $newPasswordConfirmation) {
            throw new Exception('Invalid confirmation of new password');
        }
        elseif(strlen($newPassword) < 6) {
            throw new Exception('Password has to be longer than 6 characters');
        }
    }
}