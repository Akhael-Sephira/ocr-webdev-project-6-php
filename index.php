<?php

require_once('utility/app.php');
require_once('routes/auth.php');
require_once('utility/database.php');

$config = include('config.php');

$app = new App();

Database::connect(
    $config->db['server_name'],
    $config->db['name'], 
    $config->db['username'], 
    $config->db['password']
);

$app->use('/api/auth', [$authRouter, 'use']);

$app->notFound();

?>
