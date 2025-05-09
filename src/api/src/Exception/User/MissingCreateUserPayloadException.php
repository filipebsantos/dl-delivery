<?php

namespace DLDelivery\Exception\User;

use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;
use DLDelivery\Exception\ErrorCode;

class MissingCreateUserPayloadException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Missing payload data to create a new user", ErrorCode::USER_MISSING_CREATE_PAYLOAD->value);
    }
}