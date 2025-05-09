<?php

namespace DLDelivery\Domain\Interface;

use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Application\DTO\Client\LocationUpdateDTO;
use DLDelivery\Domain\Client;
use DLDelivery\Domain\Location;

interface ClientRepositoryInterface
{   
    /** @return array<Client> */
    public function list(ClientFilterDTO $dto): array;
    public function getByID(int $clientID): ?Client;
    public function create(ClientDTO $dto): Client;
    public function update(ClientDTO $dto): Client;
    public function delete(int $clientID): bool;

    public function getLocationByID(int $locationID): Location;
    public function createLocation(int $clientID, LocationDTO $dto): Location;
    public function updateLocation(LocationUpdateDTO $dto): Location;
    public function deleteLocation(int $locationID): bool;
}