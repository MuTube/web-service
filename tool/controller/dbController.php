<?php

class DbController {

    protected static $pdo;

    public static function load() {
        $db_config = ConfigHelper::getDbConfig();
        $pdoStr = sprintf("mysql:host=%s;dbname=%s", $db_config['db_host'], $db_config['db_name']);

        try {
            self::$pdo = new PDO($pdoStr, $db_config['db_user'], $db_config['db_pswd']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        catch(Exception $e) {
             throw new Exception("Mysql connexion error");
        }
    }

    public static function getTable($table) {
        try {
            $className = ucfirst(strtolower($table)) . 'DbHelper';
            return new $className();
        }
        catch(Exception $e) {
            throw new HardException("DB Error :", $e->getMessage());
        }
    }

    public static function sanitizeQueryInput($input) {
        return self::$pdo->quote($input);
    }

    public static function execQuery($query) {
        try {
            self::$pdo->exec($query);
        }
        catch(Exception $e) {
            throw new HardException("DB Error :", $e->getMessage());
        }
    }

    public static function fetch($requestQuery) {
        try {
            $result = self::$pdo->query($requestQuery);
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $result = $result->fetch();
            return $result;
        }
        catch(Exception $e) {
            throw new HardException("DB Error :", $e->getMessage());
        }
    }

    public static function fetchAll($requestQuery) {
        try {
            $result = self::$pdo->query($requestQuery);
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $result = $result->fetchAll();
            return $result;
        }
        catch(Exception $e) {
            throw new HardException("DB Error :", $e->getMessage());
        }
    }

    protected static function tableExist($table) {
        try {
            $result = self::fetchAll("SHOW TABLES");

            foreach($result as $resTable) {
                if($table == $resTable['Tables_in_'.ConfigHelper::getDbConfig()['db_name']]) {
                    return true;
                }
            }
        }
        catch(Exception $e) {
            throw new HardException("DB Error :", $e->getMessage());
        }

        return false;
    }
}