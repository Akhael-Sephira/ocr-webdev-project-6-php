<?php

try {
    define("DBLink", new PDO ( 
            "mysql:host=" . getenv("SERVER_NAME") . "; dbname=" . getenv("DB_NAME"), 
            getenv("USERNAME"), getenv("PASSWORD")
        )
    );
} catch(PDOException $e) {

}

if (!DBLink) {
    die("Connection failed: " . mysqli_connect_error());
}


?>