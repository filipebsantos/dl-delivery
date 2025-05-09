<?php

namespace DLDelivery\Infrastructure\Http;

class ResponseHelper
{
    public static function send(array $response, int $httpCode): void
    {
        http_response_code($httpCode);
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public static function info(): void
    {
        self::send(['message' => getenv("APP_NAME"), 'version' => getenv("APP_VERSION")], 200);    
    }
}
