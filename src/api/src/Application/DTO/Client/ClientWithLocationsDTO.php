<?php

namespace DLDelivery\Application\DTO\Client;

class ClientWithLocationsDTO extends ClientDTO
{
    public function __construct(int $id, string $name, public readonly array $locationDTO)
    {
        parent::__construct($id, $name);
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
            'locations' => $this->locationDTO
        ];
    }
}