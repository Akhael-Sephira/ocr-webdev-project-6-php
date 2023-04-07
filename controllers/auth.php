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

        $headers = ['alg'=>'HS256', 'typ'=>'JWT'];
        $payload = ['userId'=>$user->id,'exp'=>time() + $config->auth['jwt_exp']];
        $jwt = JWT::generate_token($headers, $payload, $config->auth['secret']);

        $res = [
            'userId' => $user->id,
            'token' => $jwt,
        ];
        echo json_encode($res);
    }
}

?>