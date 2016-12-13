<?php

class CommonDbHelper {

    public $tableName = "";

    public function getSelectorData() {}

    public function getById($id) {
        return $this->fetch("SELECT * FROM %s WHERE id = %s", array($this->tableName, DbController::sanitizeQueryInput($id)));
    }
    
    public function getList() {
        return $this->fetchAll("SELECT * FROM %s", array($this->tableName));
    }

    public function getListForIds($ids) {
        foreach($ids as $index => $id) {
            $ids[$index] = DbController::sanitizeQueryInput($id);
        }

        return $this->fetchAll("SELECT * FROM %s WHERE id IN (%s)", [$this->tableName, implode(', ', $ids)]);
    }

    public function updateById($id, $values) {
        $this->validateData($values);

        $query = "UPDATE %s SET ";
        $i = 1;

        foreach($values as $name => $value) {
            if($name != 'id') $query .= $name . "=" . DbController::sanitizeQueryInput($value);
            $query .= ($i == count($values) ? ' ' : ', ');
            $i += 1;
        }

        $query .= 'WHERE id = %s';
        $this->execQuery($query, [$this->tableName, DbController::sanitizeQueryInput($id)]);
    }

    public function removeWithId($id) {
        if(empty($id)) throw new SoftException("Missing argument 'id'");
        $this->execQuery("DELETE FROM %s WHERE id = %s", [$this->tableName, DbController::sanitizeQueryInput($id)]);
    }

    public function create($values) {
        $this->validateData($values);
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
    

    protected function execQuery($query, $params) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            DbController::execQuery($query);
        }
        else {
            throw new HardException("Runtime Error :", "Not enough or to much parameters");
        }
    }
    
    protected function fetch($query, $params) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            return DbController::fetch($query);
        }
        else {
            throw new HardException("Runtime Error :", "Not enough or to much parameters");
        }
    }

    protected function fetchAll($query, $params = []) {
        if(count($params) == substr_count($query, '%s')) {
            $query = vsprintf($query, $params);
            return DbController::fetchAll($query);
        }
        else {
            throw new HardException("Runtime Error :", "Not enough or to much parameters");
        }
    }
}