<?php

$config = include('config.php');

try {
    define("DBLink", new PDO ( 
            "mysql:host=" . $config->db['server_name'] . 
            "; dbname=" . $config->db['name'], 
            $config->db['username'], $config->db['password']
        )
    );
} catch(PDOException $e) {

}

if (!DBLink) {
    die("Connection failed: " . mysqli_connect_error());
}


?>