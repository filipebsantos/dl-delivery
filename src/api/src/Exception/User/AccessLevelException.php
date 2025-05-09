<?php

namespace DLDelivery\Exception\User;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class AccessLevelException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("User doesn't have access level for this operation", ErrorCode::USER_INSUFICIENT_ACCESS_LEVEL->value);
    }
}