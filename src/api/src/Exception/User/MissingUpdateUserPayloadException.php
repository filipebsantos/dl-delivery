<?php

namespace DLDelivery\Exception\User;

use InvalidArgumentException;
use DLDelivery\Exception\ExceptionInterface;
use DLDelivery\Exception\ErrorCode;

class MissingUpdateUserPayloadException extends InvalidArgumentException implements ExceptionInterface
{
    public function __construct() {
        parent::__construct("Missing payload data to update an user", ErrorCode::USER_MISSING_UPDATE_PAYLOAD->value);
    }
}