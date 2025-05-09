<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class RouterInvalidControllerMethodException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Resource method not found", ErrorCode::ROUTER_INVALID_CONTROLLER_METHOD->value);
    }
}