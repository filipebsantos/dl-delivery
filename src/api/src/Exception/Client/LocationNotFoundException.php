<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class LocationNotFoundException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Location not found", ErrorCode::CLIENT_LOCATION_NOT_FOUND->value);
    }
}