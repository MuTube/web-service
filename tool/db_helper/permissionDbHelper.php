<?php

class PermissionDbHelper extends CommonDbHelper {
    //init
    function __construct() {
        $this->tableName = 'permission_data';
    }

    //actions
    public function getSelectorData() {
        $permissions = $this->getList();
        $selectOptions = [];

        foreach($permissions as $permission) {
            $selectOptions[$permission['id']] = $permission['name'];
        }

        return $selectOptions;
    }
}