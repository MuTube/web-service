<?php

class RoleViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('userRole')->getBy($by, $identifier);
    }

    public static function getList() {
        return DbController::getTable('userRole')->getList();
    }

    public static function getListWithPermissionIds() {
        return DbController::getTable('userRole')->getListWithPermissionIds();
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
        $roleTable = DbController::getTable('userRole');

        $roleId = $roleTable->create(['name' => $data['name']]);
        $roleTable->updatePermissions($roleId, $data['permission_ids']);
    }


    // UPDATE

    public static function updatePermissionsBy($by, $identifier, $permissionIds) {
        DbController::getTable('userRole')->updatePermissionsBy($by, $identifier, $permissionIds);
    }


    // REMOVE

    public static function removeBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        $roleTable = DbController::getTable('userRole');
        $roleId = $roleTable->getBy($by, $identifier)['id'];

        $roleTable->removeBy('id', $roleId);
        DbController::getTable('user')->removeRoleFromUsers($roleId);
    }
}