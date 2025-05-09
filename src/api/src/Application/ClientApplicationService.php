<?php

namespace DLDelivery\Application;

use DLDelivery\Domain\Client;
use DLDelivery\Domain\Location;
use DLDelivery\Domain\Enum\UserRole;
use DLDelivery\Domain\Interface\ClientRepositoryInterface;
use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Application\DTO\Client\LocationUpdateDTO;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Application\DTO\Client\ClientWithLocationsDTO;
use DLDelivery\Exception\User\AccessLevelException;
use DLDelivery\Exception\Client\ClientNotFoundException;
use DLDelivery\Exception\Client\LocationNotFoundException;

class ClientApplicationService
{
    private ClientRepositoryInterface $repo;

    public function __construct(ClientRepositoryInterface $repository)
    {
        $this->repo = $repository;
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
            $locationsDTO[] = $location->toResponseDTO();
        }

        return new ClientWithLocationsDTO(
            $client->getID(),
            $client->getName(),
            $locationsDTO
        );
    }

    /**
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

    public function getByID(int $clientID): ClientDTO
    {
        $client = $this->repo->getByID($clientID);

        if (!$client) { throw new ClientNotFoundException; }

        return $this->toClientDTO($client);
    }

    public function create(UserDTO $authenticatedUser, ClientDTO $dto): ClientDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException;
        }

        $sanitizedDTO = new ClientDTO(
            $dto->id,
            strtoupper($dto->name)
        );
        $newClient = $this->repo->create($sanitizedDTO);

        return $this->toClientDTO($newClient);
    }

    public function update(UserDTO $authenticatedUser, ClientDTO $dto): ClientDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException;
        }

        $sanitizedDTO = new ClientDTO(
            $dto->id,
            strtoupper($dto->name)
        );
        $updateClient = $this->repo->update($sanitizedDTO);

        return $this->toClientDTO($updateClient);
    }

    public function delete(UserDTO $authenticatedUser, int $clientID): bool
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::ADMINISTRATOR)) {
            throw new AccessLevelException;
        }

        return $this->repo->delete($clientID);
    }

    public function getLocation(int $locationID): LocationResponseDTO
    {
        $location = $this->repo->getLocationByID($locationID);

        if (!$location) { throw new LocationNotFoundException; };

        return $location->toResponseDTO();
    }

    public function newLocation(int $clientID, LocationDTO $dto): LocationResponseDTO
    {
        $newLocation = $this->repo->createLocation($clientID, $dto);

        return $newLocation->toResponseDTO();
    }

    public function updateLocation(LocationUpdateDTO $dto): LocationResponseDTO
    {   
        $oldLocation = $this->repo->getLocationByID($dto->id);

        if ($oldLocation->hasHousePicture()) {

        }

        $updatedLocation = $this->repo->updateLocation($dto);

        return $updatedLocation->toResponseDTO();
    }

    public function deleteLocation(UserDTO $authenticatedUser, int $locationID): bool
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR))
        {
            throw new AccessLevelException;
        }

        return $this->repo->deleteLocation($locationID);
    }
}