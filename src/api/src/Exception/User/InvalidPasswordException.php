<?php

namespace DLDelivery\Exception\User;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class InvalidPasswordException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid password", ErrorCode::USER_INVALID_PASSWORD->value);
    }
}