<?php

class TrackHistoryDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'track_history';
    }

    protected function validateData($values) {
        // HANDLE DATA VALIDATION
    }
}