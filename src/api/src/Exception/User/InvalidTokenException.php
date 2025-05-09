<?php

namespace DLDelivery\Exception\User;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class InvalidTokenException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid authorization token", ErrorCode::USER_INVALID_JWT_TOKEN->value);
    }
}