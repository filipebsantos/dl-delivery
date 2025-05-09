<?php

namespace DLDelivery\Application\DTO\Client;

use JsonSerializable;

class ClientDTO implements JsonSerializable
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
    ) {}

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}