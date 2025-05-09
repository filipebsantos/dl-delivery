<?php

namespace DLDelivery\Application\DTO\User;

use DLDelivery\Domain\Enum\UserRole;

class AuthUserDTO
{
    public function __construct(
        public readonly int $userID,
        public readonly string $username,
        public readonly string $password
    ) {}
}