<?php

class RoleViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(($role = DbController::getTable('role')->getBy($by, $identifier)) != null) {
            return $role;
        }
        else {
            throw new Exception("Role for " . $by . " '" . $identifier . "' not found.");
        }
    }

    public static function getList() {
        return DbController::getTable('role')->getList();
    }

    public static function getListWithPermissionIds() {
        return DbController::getTable('role')->getListWithPermissionIds();
    }

    public static function getSelectorData() {
        $roles = self::getList();
        $selectOptions = [];

        foreach($roles as $role) {
            $selectOptions[$role['id']] = $role['name'];
        }

        return $selectOptions;
    }


    // ADD

    public static function add($data) {
        self::validateData($data);
        $roleTable = DbController::getTable('role');

        $roleId = $roleTable->create(['name' => $data['name']]);
        $roleTable->updatePermissionsBy('id', $roleId, $data['permission_ids']);
    }


    // UPDATE

    public static function updatePermissionsBy($by, $identifier, $permissionIds) {
        if(($trackHistory = DbController::getTable('role')->getBy($by, $identifier)) == null) {
            throw new Exception("Role for " . $by . " '" . $identifier . "' not found.");
        }

        DbController::getTable('role')->updatePermissionsBy($by, $identifier, $permissionIds);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(($role = DbController::getTable('role')->getBy($by, $identifier)) == null) {
            throw new Exception("Role for " . $by . " '" . $identifier . "' not found.");
        }

        DbController::getTable('role')->removeBy('id', $role['id']);
        UserViewModel::removeRoleFromUsersWithRoleId($role['id']);
    }


    // VALIDATION

    protected static function validateData($data) {
        if(array_key_exists('name', $data)) {
            if(empty($data['name'])) {
                throw new Exception('A name is required');
            }
        }
    }
}