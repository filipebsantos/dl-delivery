<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class RouterInvalidControllerException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Resource not found", ErrorCode::ROUTER_INVALID_CONTROLLER->value);
    }
}