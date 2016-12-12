<?php

class TrackDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'track';
    }

    public function getByMP3FilePath($mp3FilePath) {
        return $this->fetch("SELECT * FROM track WHERE mp3_filepath = %s", [DbController::sanitizeQueryInput($mp3FilePath)]);
    }

    public function updateTrackHistoryIdById($id, $trackHistoryId) {
        $this->execQuery("UPDATE track SET track_history_id = %s", [DbController::sanitizeQueryInput($trackHistoryId)]);
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