<?php

class RoleViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('role')->getBy($by, $identifier);
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
        DbController::getTable('role')->updatePermissionsBy($by, $identifier, $permissionIds);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        $roleTable = DbController::getTable('role');
        $roleId = $roleTable->getBy($by, $identifier)['id'];

        $roleTable->removeBy('id', $roleId);
        UserViewModel::removeRoleFromUsersWithRoleId($roleId);
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