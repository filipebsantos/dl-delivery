<?php

namespace DLDelivery\Exception\Client;

use DLDelivery\Exception\ErrorCode;
use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;

class ClientAlreadyExistsException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Client name or id already exists", ErrorCode::CLIENT_ALREADY_EXISTS_IN_DB->value);
    }
}