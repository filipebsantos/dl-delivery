<?php

namespace DLDelivery\Exception\User;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class UserNotFoundException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("User not found", ErrorCode::USER_NOT_FOUND->value);
    }
}