<?php 

return (object) [
    'db' => [
        'server_name' => 'localhost',
        'username' => 'root',
        'password' => '',
        'name' => 'myDB',
    ],
    'auth' => [
        'secret' => 'MySecretKey',
        'jwt_exp' => 10 * 60, // In seconds
        'max_login_attempts' => 3,
        'lock_time' => 60, // In seconds
    ]
]

?>