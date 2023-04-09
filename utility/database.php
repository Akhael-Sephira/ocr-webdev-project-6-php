<?php

class Database {
    public static $conn = NULL;
    public static function connect($server_name, $db_name, $username, $password) {
        try {
            static::$conn = new PDO ( 
                "mysql:host=" . $server_name . "; 
                dbname=" . $db_name . ";", 
                $username, $password
            );
        } catch(PDOException $e) {

        }
    }
    public static function run($sql, $params = NULL) {
        if (!static::$conn) return false;
        $stmt = static::$conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}


?>