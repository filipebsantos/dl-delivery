<?php

namespace DLDelivery\Domain;

use DLDelivery\Domain\Location;

class Client
{
    private int $id;
    private string $name;
    /**
     * @var array<Location>
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

    /** @return array<Location> */
    public function getLocations(): array
    {
        return $this->locations;
    }
}