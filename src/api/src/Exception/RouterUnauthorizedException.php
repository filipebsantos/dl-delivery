<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class RouterUnauthorizedException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Unauthorized", ErrorCode::ROUTER_UNAUTHORIZED->value);
    }
}