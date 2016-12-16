<?php

class UserDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'user';
    }

    public function getList() {
        return $this->fetchAll("
            SELECT u.*,
                r.name AS role_name
            FROM user u
            LEFT JOIN role r ON r.id = u.role_id
        ");
    }

    public function getBy($name, $identifier) {
        return $this->fetch("
            SELECT u.*,
                r.name AS role_name
            FROM user u
            LEFT JOIN role r ON r.id = u.role_id
            WHERE u.%s = %s   
        ", [$name, DbController::sanitizeQueryInput($identifier)]);
    }

    public function getByMultipleValues($data) {
        foreach($data as $label => $value) {
            $data[$label] = DbController::sanitizeQueryInput($value);
        }

        return $this->fetch("SELECT * FROM user WHERE id = %s AND username = %s AND firstname = %s AND lastname = %s",
            array($data["id"], $data["username"], $data["firstname"], $data["lastname"]));
    }

    public function updatePasswordBy($by, $identifier, $password) {
        $password = SessionController::passwordEncryption($password);
        $this->execQuery("UPDATE user SET password = %s WHERE %s = %s", [DbController::sanitizeQueryInput($password), $by, DbController::sanitizeQueryInput($identifier)]);
    }

    public function updateAPIKeyBy($by, $identifier, $key) {
        $this->execQuery("UPDATE user SET api_key = %s WHERE %s = %s", [DbController::sanitizeQueryInput($key), $by,  DbController::sanitizeQueryInput($identifier)]);
    }

    public function getPermissionsBy($by, $identifier) {
        $roleId = $this->getBy($by, $identifier)['role_id'];

        if($roleId == 0) {
            return false;
        }

        return $this->fetchAll("
          SELECT p.*
          FROM role_2_permission rp
          LEFT JOIN permission p ON p.id = rp.permission_id
          WHERE rp.role_id = %s
        ", [DbController::sanitizeQueryInput($roleId)]);
    }

    public function removeRoleFromUsers($roleId) {
        $this->execQuery("UPDATE user SET role_id = 0 WHERE role_id = %s", [DbController::sanitizeQueryInput($roleId)]);
    }
}