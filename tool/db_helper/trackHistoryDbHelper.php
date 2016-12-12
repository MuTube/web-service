<?php

class TrackHistoryDbHelper extends CommonDbHelper {
    function __construct() {
        $this->tableName = 'track_history';
    }

    public function getByYoutubeId($youtubeId) {
        return $this->fetch("SELECT * FROM track_history WHERE youtube_id = %s", [DbController::sanitizeQueryInput($youtubeId)]);
    }

    protected function validateData($values) {
        // HANDLE DATA VALIDATION
    }
}