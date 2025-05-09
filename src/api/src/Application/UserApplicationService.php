<?php

namespace DLDelivery\Application;

use DLDelivery\Application\DTO\User\CreateUserDTO;
use DLDelivery\Application\DTO\User\UpdateUserDTO;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Application\DTO\User\UserListFilterDTO;
use DLDelivery\Domain\Service\UserService;

class UserApplicationService
{
    public function __construct(private UserService $service) {}

    /** 
     * @return array<UserDTO>
     */
    public function listAllUsers(UserListFilterDTO $dto, UserDTO $authenticatedUser): array
    {
        return $this->service->listAllUsers($dto, $authenticatedUser);
    }

    public function createUser(CreateUserDTO $dto, UserDTO $authenticatedUser): UserDTO
    {
        return $this->service->createUser($dto, $authenticatedUser);
    }

    public function updateUser(UpdateUserDTO $dto, UserDTO $authenticatedUser): UserDTO
    {
        return $this->service->updateUser($dto, $authenticatedUser);
    }

    public function deleteUser(int $userID, UserDTO $authenticatedUser): bool
    {
        return $this->service->deleteUser($userID, $authenticatedUser);
    }

    public function getUserByID(int $userID, UserDTO $authenticatedUser): UserDTO
    {
        return $this->service->getUserByID($userID, $authenticatedUser);
    }

    public function getUserByUsername(string $username): UserDTO
    {
        return $this->service->getUserByUsername($username);
    }
}