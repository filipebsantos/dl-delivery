<?php

namespace DLDelivery\Application;

use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Application\DTO\Client\LocationUpdateDTO;
use DLDelivery\Domain\Service\ClientService;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Domain\Enum\UserRole;
use DLDelivery\Exception\User\AccessLevelException;

class ClientApplicationService
{
    private ClientService $service;

    public function __construct(ClientService $clientService)
    {
        $this->service = $clientService;    
    }

    /**
     * @return array<ClientDTO>
     */
    public function listAll(ClientFilterDTO $dto): array
    {
        return $this->service->listAll($dto);
    }

    public function getByID(int $clientID): ClientDTO
    {
        return $this->service->getByID($clientID);
    }

    public function create(UserDTO $authenticatedUser, ClientDTO $dto): ClientDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException;
        }

        return $this->service->create($dto);
    }

    public function update(UserDTO $authenticatedUser, ClientDTO $dto): ClientDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException;
        }

        return $this->service->update($dto);
    }

    public function delete(UserDTO $authenticatedUser, int $clienID): bool
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::ADMINISTRATOR)) {
            throw new AccessLevelException;
        }

        return $this->service->delete($clienID);
    }

    public function getLocation(int $locationID): LocationResponseDTO
    {
        return $this->service->getLocation($locationID);
    }

    public function newLocation(int $clientID, LocationDTO $dto): LocationResponseDTO
    {
        return $this->service->newLocation($clientID, $dto);
    }

    public function updateLocation(int $clientID, LocationUpdateDTO $dto): LocationResponseDTO
    {
        return $this->service->updateLocation($clientID, $dto);
    }

    public function deleteLocation(UserDTO $authenticatedUser, int $locationID): bool
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR))
        {
            throw new AccessLevelException;
        }

        return $this->service->deleteLocation($locationID);
    }
}