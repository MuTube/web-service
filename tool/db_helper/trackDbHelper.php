<?php

class TrackDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'track';
    }

    public function getOldest() {
        $oldestTrackId = $this->fetch("SELECT MIN(id) FROM track", [])['MIN(id)'];
        return $this->getBy('id', $oldestTrackId);
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
}