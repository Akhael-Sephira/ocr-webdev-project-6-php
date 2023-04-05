<?php

require_once('./utility/router.php');
require_once('./utility/database.php');
require_once('./controllers/auth.php');

$authRouter = new Router();

$authRouter->post('/signup', [AuthController::class, 'signup']);
$authRouter->post('/login', [AuthController::class, 'login']);

?>

