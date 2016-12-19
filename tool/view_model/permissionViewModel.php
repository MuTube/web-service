<?php

class PermissionViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(($permission = DbController::getTable('permission')->getBy($by, $identifier)) != null) {
            return $permission;
        }
        else {
            throw new Exception("Permission for " . $by . " '" . $identifier . "' not found.");
        }
    }

    public static function getList() {
        return DbController::getTable('permission')->getList();
    }

    public static function getSelectorData() {
        $permissions = self::getList();
        $selectOptions = [];

        foreach($permissions as $permission) {
            $selectOptions[$permission['id']] = $permission['name'];
        }

        return $selectOptions;
    }


    // ADD

    // HANDLE CREATION


    // REMOVE

    // HANDLE DELETION
}