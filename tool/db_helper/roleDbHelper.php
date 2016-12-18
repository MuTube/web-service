<?php

class RoleDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'role';
    }

    public function getListWithPermissionIds() {
        $roles = parent::getList();

        foreach($roles as $index => $role) {
            $permissions = $this->fetchAll("
                SELECT rp.permission_id
                FROM role_2_permission rp
                WHERE role_id = %s
            ", [DbController::sanitizeQueryInput($role["id"])]);

            foreach($permissions as $index2 => $permission) $roles[$index]['permission_ids'][$index2] = $permission['permission_id'];
        }

        return $roles;
    }

    public function updatePermissionsBy($by, $identifier, $permissionIds) {
        $role = $this->getBy($by, $identifier);
        $this->execQuery("DELETE FROM role_2_permission WHERE role_id = %s", DbController::sanitizeQueryInput($role['id']));

        foreach ($permissionIds as $permissionId) {
            $this->execQuery("INSERT INTO role_2_permission (role_id, permission_id) VALUES (%s, %s)", [
                DbController::sanitizeQueryInput($role['id']),
                DbController::sanitizeQueryInput($permissionId)
            ]);
        }
    }

    public function removeBy($by, $identifier) {
        $role = $this->getBy($by, $identifier);

        $this->execQuery("DELETE FROM role_2_permission WHERE role_id = %s", DbController::sanitizeQueryInput($role['id']));
        parent::removeBy('id', $role['id']);
    }
}