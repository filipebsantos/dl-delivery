<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class ClientNotFoundException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Client not found", ErrorCode::CLIENT_NOT_FOUND->value);
    }
}