<?php

namespace DLDelivery\Domain;

use DLDelivery\Application\DTO\Client\LocationResponseDTO;

class Client
{
    private int $id;
    private string $name;
    /**
     * @var array<LocationResponseDTO>
     */
    private array $locations = [];

    public function __construct(int $id, string $name, array $locations = [])
    {
        $this->id = $id;
        $this->name = strtoupper($name);
        $this->locations = $locations;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return array<LocationResponseDTO> */
    public function getLocations(): array
    {
        return $this->locations;
    }
}