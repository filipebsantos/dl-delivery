<?php

namespace DLDelivery\Exception\User;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class UserAlreadyExistsException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Username already exists in database", ErrorCode::USER_ALREADY_EXISTS->value);
    }
}