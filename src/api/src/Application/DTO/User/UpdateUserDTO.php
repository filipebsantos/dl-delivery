<?php

namespace DLDelivery\Application\DTO\User;

use DLDelivery\Domain\Enum\UserRole;

class UpdateUserDTO
{
    public function __construct(
        public readonly int $userID,
        public readonly ?int $erpUserID = null,
        public readonly ?string $name = null,
        public readonly ?string $password = null,
        public readonly ?UserRole $access = null
    ) {}
}