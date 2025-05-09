<?php

namespace DLDelivery\Infrastructure\Logger;

class TrackIDProvider
{
    private static string $trackID;

    public static function generate()
    {
        self::$trackID = bin2hex(random_bytes(8));
    }

    public static function get(): ?string
    {
        return self::$trackID;
    }
}