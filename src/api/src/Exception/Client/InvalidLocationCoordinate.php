<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class InvalidLocationCoordinate extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid location coordinates", ErrorCode::CLIENT_INVALID_LOCATION_COORDINATE->value);
    }
}