<?php

require_once('utility/app.php');
require_once('routes/auth.php');

$app = new App();

$app->use('/api/auth', [$authRouter, 'use']);

$app->notFound();

?>
