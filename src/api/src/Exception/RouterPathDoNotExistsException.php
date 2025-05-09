<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class RouterPathDoNotExistsException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid URI", ErrorCode::ROUTER_PATH_NOT_EXISTS->value);
    }
}