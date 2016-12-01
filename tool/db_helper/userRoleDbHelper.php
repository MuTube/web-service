<?php

class UserRoleDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'usr_role';
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

    public function getSelectorData() {
        $roles = $this->getList();
        $selectOptions = [];

        foreach($roles as $role) {
            $selectOptions[$role['id']] = $role['name'];
        }

        return $selectOptions;
    }

    public function updatePermissions($roleId, $permission_ids) {
        $this->execQuery("DELETE FROM role_2_permission WHERE role_id = %s", DbController::sanitizeQueryInput($roleId));

        foreach ($permission_ids as $permission_id) {
            $this->execQuery("INSERT INTO role_2_permission (role_id, permission_id) VALUES (%s, %s)", [
                DbController::sanitizeQueryInput($roleId),
                DbController::sanitizeQueryInput($permission_id)
            ]);
        }
    }

    public function removeWithId($roleId){
        $this->execQuery("DELETE FROM role_2_permission WHERE role_id = %s", DbController::sanitizeQueryInput($roleId));
        parent::removeWithId($roleId);
    }

    protected function validateData($values) {
        if(array_key_exists('name', $values)) {
            if(empty($values['name'])) {
                throw new SoftException('A name is required');
            }
        }
    }
}