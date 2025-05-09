<?php

namespace DLDelivery\Exception;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class InvalidJsonException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Invalid json input", ErrorCode::INVALID_JSON->value);
    }
}