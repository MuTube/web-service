<?php

class PermissionViewModel {
    // GET

    public static function getBy($by, $identifier) {
        if(empty($identifier)) {
            throw new Exception("No " . $by . " provided");
        }

        return DbController::getTable('permission')->getBy($by, $identifier);
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