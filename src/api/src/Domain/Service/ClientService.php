<?php

namespace DLDelivery\Domain\Service;

use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\ClientWithLocationsDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Application\DTO\Client\LocationUpdateDTO;
use DLDelivery\Domain\Client;
use DLDelivery\Domain\Interface\ClientRepositoryInterface;
use DLDelivery\Exception\Client\ClientNotFoundException;
use DLDelivery\Exception\Client\LocationNotFoundException;

class ClientService
{
    private ClientRepositoryInterface $repo;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->repo = $clientRepository;
    }

    private function toClientDTO(Client $client): ClientDTO|ClientWithLocationsDTO
    {   
        if (count($client->getLocations()) == 0) {
            return new ClientDTO(
                $client->getID(),
                $client->getName()
            );
        }

        $locationsDTO = [];
        foreach ($client->getLocations() as $location) {
            $locationsDTO[] = $location;
        }

        return new ClientWithLocationsDTO(
            $client->getID(),
            $client->getName(),
            $locationsDTO
        );
    }

    /**
     * Returns an array of ClientDTO WITHOUT ANY locations! Only ID and name returned.
     *  
     * @return array<ClientDTO>
     */
    public function listAll(ClientFilterDTO $dto): array
    {
        $clients = $this->repo->list($dto);

        foreach($clients['clients'] as &$client) {
            $client = $this->toClientDTO($client);
        }
        unset($client);

        return $clients;
    }

    /**
     * Return a ClientDTO with all locations for this client
     */
    public function getByID(int $clientID): ClientWithLocationsDTO
    {
        $client = $this->repo->getByID($clientID);

        if (!$client) { throw new ClientNotFoundException; }

        return $this->toClientDTO($client);
    }

    public function create(ClientDTO $dto): ClientDTO
    {
        $sanitizedDTO = new ClientDTO(
            $dto->id,
            strtoupper($dto->name)
        );
        $newClient = $this->repo->create($sanitizedDTO);

        return $this->toClientDTO($newClient);
    }

    public function update(ClientDTO $dto): ClientDTO
    {
        $sanitizedDTO = new ClientDTO(
            $dto->id,
            strtoupper($dto->name)
        );
        $updateClient = $this->repo->update($sanitizedDTO);

        return $this->toClientDTO($updateClient);
    }

    public function delete(int $clientID): bool
    {
        return $this->repo->delete($clientID);
    }

    public function getLocation(int $locationID): LocationResponseDTO
    {
        $location = $this->repo->getLocationByID($locationID);

        if (!$location) { throw new LocationNotFoundException; };

        return $location;
    }

    public function newLocation(int $clienID, LocationDTO $dto): LocationResponseDTO
    {
        $newLocation = $this->repo->createLocation($clienID, $dto);

        return $newLocation;
    }

    public function deleteLocation(int $locationID): bool
    {
        return $this->repo->deleteLocation($locationID);
    }

    public function updateLocation(int $clientID, LocationUpdateDTO $dto): LocationResponseDTO
    {
        $updatedLocation = $this->repo->updateLocation($clientID, $dto);

        return $updatedLocation;
    }
}