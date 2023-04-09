<?php

require_once('utility/app.php');
require_once('routes/auth.php');
require_once('utility/database.php');

$app = new App();

Database::connect(
    getenv("SERVER_NAME"),
    getenv("DB_NAME"), 
    getenv("USERNAME"), 
    getenv("PASSWORD")
);

$app->use('/api/auth', [$authRouter, 'use']);

$app->notFound();

?>
