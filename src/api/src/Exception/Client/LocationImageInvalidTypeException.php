<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class LocationImageInvalidTypeException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid image type for location image", ErrorCode::CLIENT_LOCATION_IMAGE_INVALID_TYPE->value);
    }
}