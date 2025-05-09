<?php

namespace DLDelivery\Application\DTO;

use DLDelivery\Domain\Enum\UserRole;

class JwtDTO
{
    public function __construct(
        public readonly int $sub,
        public readonly ?int $erpUserID,
        public readonly string $name,
        public readonly string $username,
        public readonly UserRole $role,
        public readonly int $iat,
        public readonly int $exp
    ) {}

    public function toArray(): array
    {
        return [
            'sub' => $this->sub,
            'erpUserID' => $this->erpUserID,
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role->value,
            'iat' => $this->iat,
            'exp' => $this->exp
        ];
    }

    public static function fromStdClass(object $payload): JwtDTO
    {
        return new self(
            $payload->sub,
            $payload->erpUserID,
            $payload->name,
            $payload->username,
            UserRole::from($payload->role),
            $payload->iat,
            $payload->exp
        );
    }
}
