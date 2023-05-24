<?php

require 'vendor/autoload.php';

use Firebase\JWT\JWT;

class Auth
{
    private static $secretKey = "your_secret_key";

    public static function generateToken($userId)
    {
        $payload = array(
            "iss" => "your_issuer",
            "aud" => "your_audience",
            "iat" => time(),
            "exp" => time() + (60 * 60), // Token expiration time (1 hour)
            "userId" => $userId
        );

        return JWT::encode($payload, self::$secretKey, 'HS256');
    }

    public static function verifyToken($token)
    {
        try {
            $decoded = JWT::decode($token, self::$secretKey, array('HS256'));
            return $decoded->userId;
        } catch (Exception $e) {
            return null;
        }
    }
}