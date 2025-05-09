<?php

namespace DLDelivery\Application\DTO\User;

use DLDelivery\Domain\Enum\UserRole;

class UserDTO
{
    public function __construct(
        public readonly int $userID,
        public readonly ?int $erpUserID,
        public readonly string $name,
        public readonly string $username,
        public readonly UserRole $access
    ) {}

    public function toArray(): array
    {
        return [
            "userID" => $this->userID,
            "erpUserID" => $this->erpUserID,
            "name" => $this->name,
            "username" => $this->username,
            "access" => $this->access->value
        ];
    }
}