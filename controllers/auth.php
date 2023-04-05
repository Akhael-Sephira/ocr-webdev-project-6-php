<?php

include('./models/user.php');
include('./utility/jwt.php');

define('JWT_EXP_TIME', 60*60);

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

        $headers = ['alg'=>'HS256', 'typ'=>'JWT'];
        $payload = ['userId'=>$user->id,'exp'=>time() + JWT_EXP_TIME];
        $jwt = JWT::generate_token($headers, $payload, getenv('SECRET_KEY'));

        $res = [
            'userId' => $user->id,
            'token' => $jwt,
        ];
        echo json_encode($res);
    }
}

?>