<?php

class UserDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'usr_data';
    }

    public function getList() {
        return $this->fetchAll("
            SELECT u.*,
                r.name AS role_name
            FROM usr_data u
            LEFT JOIN usr_role r ON r.id = u.role_id
        ");
    }

    public function getById($id) {
        return $this->fetch("
            SELECT u.*,
                r.name AS role_name
            FROM usr_data u
            LEFT JOIN usr_role r ON r.id = u.role_id
            WHERE u.id = %s   
        ", [$id]);
    }

    public function getForUsername($usrname) {
        return $this->fetch("SELECT * FROM %s WHERE usrname=%s", array($this->tableName, DbController::sanitizeQueryInput($usrname)));
    }

    public function getForAPIKey($api_key) {
        return $this->fetch("SELECT * FROM %s WHERE api_key=%s", array($this->tableName, DbController::sanitizeQueryInput($api_key)));
    }

    public function getForFullUserData($data) {
        foreach($data as $label => $value) {
            $data[$label] = DbController::sanitizeQueryInput($value);
        }

        return $this->fetch("SELECT * FROM %s WHERE id=%s AND usrname=%s AND firstname=%s AND lastname=%s",
            array($this->tableName, $data["id"], $data["usrname"], $data["firstname"], $data["lastname"]));
    }

    public function updatePasswordForUid($uid, $password) {
        $password = SessionController::passwordEncryption($password);
        $this->execQuery("UPDATE %s SET pswd=%s WHERE id= %s", [$this->tableName, DbController::sanitizeQueryInput($password), DbController::sanitizeQueryInput($uid)]);
    }

    public function updateAPIKeyForUid($uid, $key) {
        $this->execQuery("UPDATE %s SET api_key=%s WHERE id= %s", [$this->tableName, DbController::sanitizeQueryInput($key), DbController::sanitizeQueryInput($uid)]);
    }

    public function getPermissionsForUid($userId) {
        $roleId = $this->getById($userId)['role_id'];

        if($roleId == 0) {
            return false;
        }

        return $this->fetchAll("
          SELECT p.*
          FROM role_2_permission rp
          LEFT JOIN permission_data p ON p.id = rp.permission_id
          WHERE rp.role_id = %s
        ", [DbController::sanitizeQueryInput($roleId)]);
    }

    public function removeDeletedRoleFromUsers($roleId) {
        $this->execQuery("UPDATE %s SET role_id = 0 WHERE role_id = %s", [$this->tableName, DbController::sanitizeQueryInput($roleId)]);
    }

    public function validatePasswordReset($data) {
        if($data['newPassword'] != $data['retypeNewPassword']) {
            throw new SoftException('Invalid confirmation of new password');
        }
        elseif(strlen($data['newPassword']) < 6) {
            throw new SoftException('Password has to be longer than 6 characters');
        }
    }

    protected function validateData($values) {
        if(array_key_exists('email', $values)) {
            if(!filter_var($values['email'], FILTER_VALIDATE_EMAIL) && !empty($values['email'])) {
                throw new SoftException('Invalid email adress');
            }
        }

        if(array_key_exists('usrname', $values)) {
            if(empty($values['usrname'])) {
                throw new SoftException('A username is required');
            }
        }

        if(array_key_exists('role_id', $values)) {
            if(empty($values['role_id'])) {
                throw new SoftException('A role is required');
            }
        }
    }
}