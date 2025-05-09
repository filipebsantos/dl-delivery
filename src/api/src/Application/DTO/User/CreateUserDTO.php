<?php

namespace DLDelivery\Application\DTO\User;

use DLDelivery\Domain\Enum\UserRole;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $password,
        public readonly UserRole $access,
        public readonly ?int $erpUserID = null
    ) {}
}