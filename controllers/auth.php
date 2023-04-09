<?php

include('./models/user.php');
include('./utility/jwt.php');


class AuthController {
    static public function signup() {
        $user = new User([
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
        ]);
        $user->save();
    }
    
    static public function login() {
        $user = User::getAuthenticated(
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );
        if (!$user) return;

        $config = include('./config.php');

        $jwt = new JWT([
            'userId'=>$user->id,
            'exp'=>time() + JWT_EXP_TIME
        ]);

        $res = [
            'userId' => $user->id,
            'token' => $jwt->sign(getenv('SECRET_KEY')),
        ];
        echo json_encode($res);
    }
}

?>