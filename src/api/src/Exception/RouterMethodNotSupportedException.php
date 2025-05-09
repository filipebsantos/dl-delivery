<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class RouterMethodNotSupportedException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("HTTP method not supported", ErrorCode::ROUTER_METHOD_NOT_SUPPORTED->value);
    }
}