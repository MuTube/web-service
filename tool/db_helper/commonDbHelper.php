<?php

class CommonDbHelper {

    public $tableName = "";

    public function getBy($by, $identifier) {
        return $this->fetch("SELECT * FROM %s WHERE %s = %s", [$this->tableName, $by, DbController::sanitizeQueryInput($identifier)]);
    }
    
    public function getList() {
        return $this->fetchAll("SELECT * FROM %s", [$this->tableName]);
    }

    public function getListForIds($ids) {
        foreach($ids as $index => $id) {
            $ids[$index] = DbController::sanitizeQueryInput($id);
        }

        return $this->fetchAll("SELECT * FROM %s WHERE id IN (%s)", [$this->tableName, implode(', ', $ids)]);
    }

    public function updateBy($by, $identifier, $values) {
        $query = "UPDATE %s SET ";
        $i = 1;

        foreach($values as $name => $value) {
            if($name != 'id') $query .= $name . "=" . DbController::sanitizeQueryInput($value);
            $query .= ($i == count($values) ? ' ' : ', ');
            $i += 1;
        }

        $query .= 'WHERE %s = %s';
        $this->execQuery($query, [$this->tableName, $by, DbController::sanitizeQueryInput($identifier)]);
    }

    public function removeBy($by, $identifier) {
        $this->execQuery("DELETE FROM %s WHERE %s = %s", [$this->tableName, $by, DbController::sanitizeQueryInput($identifier)]);
    }

    public function create($values) {
        $valuesNames = array_keys($values);

        foreach($values as $index => $value) {
            $values[$index] = DbController::sanitizeQueryInput($value);
        }

        $this->execQuery("INSERT INTO %s (%s) VALUES (%s)", [$this->tableName,  implode(',', $valuesNames), implode(",", $values)]);
        $data = $this->fetch("SELECT id FROM %s WHERE %s = %s", [$this->tableName, $valuesNames[0], $values[$valuesNames[0]]]);

        return $data['id'];
    }

    public function removeWithIds($ids) {
        $ids = is_array($ids) ? $ids : [$ids];
        $query = "DELETE FROM %s WHERE ";

        foreach($ids as $index => $id) {
            if($index != 0) $query .= ' OR ';
            $query .= "id = " . DbController::sanitizeQueryInput($id);
        }

        $this->execQuery($query, [$this->tableName]);
    }
    

    // BASE DB INTERACTION METHODS

    protected function execQuery($query, $params) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            DbController::execQuery($query);
        }
        else {
            throw new Exception("Query build Error :", "Not enough or to much parameters");
        }
    }
    
    protected function fetch($query, $params) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            return DbController::fetch($query);
        }
        else {
            throw new Exception("Query build Error :", "Not enough or to much parameters");
        }
    }

    protected function fetchAll($query, $params = []) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            return DbController::fetchAll($query);
        }
        else {
            throw new Exception("Query build Error :", "Not enough or to much parameters");
        }
    }
}