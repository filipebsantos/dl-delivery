<?php

namespace DLDelivery\Interface\Http;

use DLDelivery\Application\ClientApplicationService;
use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Exception\Client\ClientNotFoundException;
use DLDelivery\Exception\Client\LocationNotFoundException;
use DLDelivery\Exception\ExceptionHandler;
use DLDelivery\Infrastructure\Http\ResponseHelper;
use DLDelivery\Interface\Validator\ClientRequestValidator;

class ClientController
{
    public function __construct(private ClientApplicationService $clientService, private LoggerInterface $logger) {}

    public function createClient(?UserDTO $authUser): void
    {
        try {
            $clientDTO = ClientRequestValidator::create();
            $client = $this->clientService->create($authUser, $clientDTO);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        ResponseHelper::send($client->toArray(), 200);
    }

    public function getByID(UserDTO $authUser, int $id): void
    {
        $client = $this->clientService->getByID($id);

        ResponseHelper::send($client->toArray(), 200);
    }

    public function delete(UserDTO $authUser, int $id): void
    {
        $client = $this->clientService->delete($authUser, $id);

        if ($client) {
            ResponseHelper::send(['message' => 'Client deleted'], 200);
        } else {
            throw new ClientNotFoundException;
        }
    }

    public function update(UserDTO $authUser, int $id): void
    {
        $client = ClientRequestValidator::update($id);
        $updateClient = $this->clientService->update($authUser, $client);

        ResponseHelper::send($updateClient->toArray(), 200);
    }

    public function list(UserDTO $authUser): void
    {
        $filter = ClientRequestValidator::list();
        $filteredClient = $this->clientService->listAll($filter);

        ResponseHelper::send($filteredClient, 200);
    }

    public function createLocation(UserDTO $authUser, int $id): void
    {
        $location = ClientRequestValidator::createLocation();
        $newLocation = $this->clientService->newLocation($id, $location);

        ResponseHelper::send($newLocation->jsonSerialize(), 200);
    }

    public function getLocation(UserDTO $authUser, int $id): void
    {
        $location = $this->clientService->getLocation($id);

        ResponseHelper::send($location->jsonSerialize(), 200);
    }

    public function deleteLocation(UserDTO $authUser, int $id): void
    {
        $deleteLocation = $this->clientService->deleteLocation($authUser, $id);

        if ($deleteLocation) {
            ResponseHelper::send(['message' => 'Location deleted'], 200);
        } else {
            throw new LocationNotFoundException;
        }
    }
}