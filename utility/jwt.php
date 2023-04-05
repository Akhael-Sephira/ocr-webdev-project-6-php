<?php

class JWT {

    public static function generate_token($headers, $payload, $secret = 'secret_key') {
        
        $headers_encoded = base64url_encode(json_encode($headers));
        $payload_encoded = base64url_encode(json_encode($payload));
        
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = base64url_encode($signature);
        
        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        
        return $jwt;
    } 

    public static function validate_token($jwt, $secret = 'secret_key') {
        // split the jwt
        $token_parts = explode('.', $jwt);
        $header = base64_decode($token_parts[0]);
        $payload = base64_decode($token_parts[1]);
        $signature_provided = $token_parts[2];

        // Check exp time
        $expiration = json_decode($payload)->exp;
        $is_expired = ($expiration - time()) < 0;

        if ($is_expired) return false;

        // Build signature to compare
        $headers_encoded = base64url_encode($header);
        $payload_encoded = base64url_encode($payload);
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
        $signature_encoded = base64url_encode($signature);

        $is_valid = ($signature_provided === $signature_encoded);

        return $is_valid;

    }

}

function base64url_encode($str) {
    return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
}

?>