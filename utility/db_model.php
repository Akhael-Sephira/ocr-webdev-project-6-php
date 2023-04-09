<?php 

require_once('database.php');

class DBModel {

    public static $schema = [
        'id'=>'INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'reg_date'=>'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
    ];

    function __construct($args) {
        foreach(static::$schema as $key => $value) {
            $this->{$key} = $args[$key] ?? NULL;
        }
    }

    public static function getTableName() {
        return static::class . "s";
    }

    public static function find($where, $params, $limit = 1) {
        try {
            $result = Database::run(
                "SELECT * FROM " .
                static::getTableName() . 
                " WHERE $where LIMIT $limit", 
                $params
            )->fetch();
            if (!$result) return false;
            $object = new static($result); 
            return $object;
        } catch(PDOException) {
            return false;
        }
    }
    /** save instance on database */
    public function save() {
        if(!static::verifyTable()) return false;
        if(!$this->preSave()) return false;

        try {
            $keys = array_keys(static::$schema);
            $sql = "INSERT INTO Users (" . join(",", $keys) . ") 
                VALUES (". join(",", array_map(function($n) {return ":" . $n;}, $keys)) .")";
            $params = [];
            foreach($keys as $key) {
                $params[":" . $key] = $this->{$key};
            };
            Database::run($sql, $params);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    /** update all given keys of the instance on database */
    public function update($keys) {
        try {
            $sql = "UPDATE Users SET " . 
                join(",", array_map(function($n) {return $n . " = :" . $n;}, $keys)) 
                . " WHERE id=:id";
            $params = [":id" => $this->id];
            foreach($keys as $key) {
                $params[":" . $key] = $this->{$key};
            }
            Database::run($sql, $params);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    /** Function invoked before saving the instance on the database, if false, save() will be cancelled */
    public function preSave() {
        // Put tests, conditions & other here.
        return true;
    }
    /** Check if Model table exist - if not invoke static function createTable() */
    public static function verifyTable() {
        try {
            Database::run( "DESCRIBE `" . static::getTableName() . "`");
            return true;    
        } catch(PDOException $e) {
            if ($e->getCode() !== "42S02") return false;
            return static::createTable();
        }
    }
    /** Try to create a table based on Model schema */
    public static function createTable() {
        try {
            $sql = "CREATE TABLE " . static::getTableName() . " (";
            foreach(static::$schema as $key => $value) {
                $sql = $sql . " " . $key . " " . $value . ",";
            }
            $sql = substr($sql, 0, strlen($sql) - 1);
            $sql = $sql . ")";
            Database::run->prepare($sql);
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}

?>