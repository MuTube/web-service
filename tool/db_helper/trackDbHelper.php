<?php

class TrackDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'track';
    }

    public function removeWithTrackHistoryId($trackHistoryIds) {
        $ids = is_array($trackHistoryIds) ? $trackHistoryIds : [$trackHistoryIds];
        $query = "DELETE FROM track WHERE ";

        foreach($ids as $index => $id) {
            if($index != 0) $query .= ' OR ';
            $query .= "id = " . DbController::sanitizeQueryInput($id);
        }

        $this->execQuery($query, []);
    }

    protected function validateData($values) {
        // HANDLE DATA VALIDATION
    }
}