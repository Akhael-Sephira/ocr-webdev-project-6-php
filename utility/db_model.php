<?php 

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

    public static function find($where, $args, $parameters) {
        try {
            $stmt = DBLink->prepare("SELECT * FROM" .
             " " . static::getTableName() . 
             " WHERE " . $where .
             " " . $parameters
            );
            foreach($args as $key => $value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            $result = $stmt->fetch();
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
            $stmt = DBLink->prepare("INSERT INTO Users (" . join(",", $keys) . ") 
                VALUES (". join(",", array_map(function($n) {return ":" . $n;}, $keys)) .")");
            foreach($keys as $key) {
                $stmt->bindParam(":" . $key, $this->{$key});
            }
            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    /** update all given keys of the instance on database */
    public function update($keys) {
        try {
            $stmt = DBLink->prepare("UPDATE Users SET " . 
                join(",", array_map(function($n) {return $n . " = :" . $n;}, $keys)) 
                . " WHERE id=:id");
            $stmt->bindParam(":id", $this->id);
            foreach($keys as $key) {
                $stmt->bindParam(":" . $key, $this->{$key});
            }
            $stmt->execute();
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
            $stmt = DBLink->prepare( "DESCRIBE `" . static::getTableName() . "`");
            $stmt->execute();
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
            $stmt = DBLink->prepare($sql);
            $stmt->execute();
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}

?>