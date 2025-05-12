<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class LocationImageInvalidSizeException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid image size for location image", ErrorCode::CLIENT_LOCATION_IMAGE_INVALID_SIZE->value);
    }
}