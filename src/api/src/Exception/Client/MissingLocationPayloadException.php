<?php

namespace DLDelivery\Exception\Client;

use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;
use DLDelivery\Exception\ErrorCode;

class MissingLocationPayloadException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Missing payload data to create a new client", ErrorCode::CLIENT_LOCATION_MISSING_PAYLOAD->value);
    }
}