<?php

class JWT {

    public function __construct($payload = []) {
        $this->header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        $this->payload = $payload;
        $this->signature = NULL;
    }

    /** Create a JWT instance based on a string token */
    public static function withToken($token) {
        $instance = new self();

        $token_parts = explode('.', $token);
        $instance->header = json_decode(base64_decode($token_parts[0]), true);
        $instance->payload = json_decode(base64_decode($token_parts[1]), true);
        $instance->signature = base64_decode($token_parts[2]);

        return $instance;
    }

    /** Create the signature using SHA256, returning it along encoded header & payload */
    private function create_signature($secret) {
        $encoded_header = JWT::base64url_encode(json_encode($this->header));
        $encoded_payload = JWT::base64url_encode(json_encode($this->payload));

        $signature = hash_hmac(
            "SHA256",
            "$encoded_header.$encoded_payload",
            $secret,
            true
        );
        return [
            'encoded_header' => $encoded_header,
            'encoded_payload' => $encoded_payload,
            'signature' => $signature,
        ];
    }

    /** Return a signed token */
    public function sign($secret = 'secret_key') : string {
        [
            'encoded_header' => $encoded_header,
            'encoded_payload' => $encoded_payload,
            'signature' => $signature,
        ] = $this->create_signature($secret);

        $this->signature = $signature;
        $encoded_signature = JWT::base64url_encode($this->signature);

        $jwt = "$encoded_header.$encoded_payload.$encoded_signature";

        return $jwt;
    }

    /** Return true if the token isn't expired */
    public function is_expired() : bool {
        if (!$this->payload["exp"]) return false;
        return ($this->payload["exp"] - time()) < 0;
    }

    /** Return true if the signature is valid */
    public function is_valid($secret = 'secret_key') : bool {
        [
            'encoded_header' => $encoded_header,
            'encoded_payload' => $encoded_payload,
            'signature' => $signature,
        ] = $this->create_signature($secret);

        return $signature === $this->signature;
    }

    /** Base64URL encode the given string */
    static function base64url_encode($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}


?>