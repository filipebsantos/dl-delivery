<?php

namespace DLDelivery\Exception\Client;

use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;
use DLDelivery\Exception\ErrorCode;

class MissingCreateClientPayloadException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Missing payload data to create a new client", ErrorCode::CLIENT_MISSING_CREATE_PAYLOAD->value);
    }
}