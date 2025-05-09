<?php

namespace DLDelivery\Domain\Interface;

use DLDelivery\Application\DTO\User\CreateUserDTO;
use DLDelivery\Application\DTO\User\UpdateUserDTO;
use DLDelivery\Application\DTO\User\UserListFilterDTO;
use DLDelivery\Domain\User;

interface UserRepositoryInterface 
{
    /** 
     * @return array<User>
     */
    public function listUsers(UserListFilterDTO $dto): array;       
    public function createUser(CreateUserDTO $dto): User;
    public function updateUser(UpdateUserDTO $dto): User;
    public function deleteUser(int $userID): bool;
    public function getUserByID(int $userID): User;
    public function getUserByUsername(string $username): User;
}
