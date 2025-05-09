<?php

namespace DLDelivery\Domain\Service;

use DLDelivery\Domain\User;
use DLDelivery\Application\DTO\User\AuthUserDTO;
use DLDelivery\Application\DTO\User\CreateUserDTO;
use DLDelivery\Application\DTO\User\UpdateUserDTO;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Application\DTO\User\UserListFilterDTO;
use DLDelivery\Domain\Enum\UserRole;
use DLDelivery\Domain\Interface\UserRepositoryInterface;
use DLDelivery\Exception\User\AccessLevelException;
use DLDelivery\Exception\User\UserNotFoundException;

class UserService
{
    private UserRepositoryInterface $repo;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->repo = $userRepository;
    }

    private function toUserDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getID(),
            $user->getErpUserID(),
            $user->getName(),
            $user->getUsername(),
            $user->getAccess()
        );
    }

    /** 
     * @return array<UserDTO>
     */
    public function listAllUsers(UserListFilterDTO $dto, UserDTO $authenticatedUser): ?array
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException();
        }

        $users = $this->repo->listUsers($dto);

        $response = [];

        foreach ($users as $user) {
            $response[] = $this->toUserDTO($user);
        }

        return $response;
    }

    public function getUserByID(int $userID, UserDTO $authenticatedUser): UserDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::OPERATOR)) {
            throw new AccessLevelException();
        }
        
        $user = $this->repo->getUserByID($userID);

        if(!$user) { throw new UserNotFoundException(); }

        return $this->toUserDTO($user);
    }

    public function getUserByUsername(string $username): UserDTO
    {       
        $user = $this->repo->getUserByUsername($username);

        if(!$user) { throw new UserNotFoundException(); }

        return $this->toUserDTO($user);
    }

    public function createUser(CreateUserDTO $dto, UserDTO $authenticatedUser): ?UserDTO
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::ADMINISTRATOR)) {
            throw new AccessLevelException();
        }

        $sanitizedDTO = new CreateUserDTO(
            $dto->name,
            $dto->username,
            $this->hashPassword($dto->password),
            $dto->access,
            $dto->erpUserID
        );

        $user = $this->repo->createUser($sanitizedDTO);

        return $this->toUserDTO($user);
    }

    public function updateUser(UpdateUserDTO $dto, UserDTO $authenticatedUser): ?UserDTO
    {
        $isAdmin = $authenticatedUser->access->hasAccessLevel(UserRole::ADMINISTRATOR);
        $isSelf =  $authenticatedUser->userID === $dto->userID;

        if (!$isAdmin && !$isSelf) {
            throw new AccessLevelException();
        }

        $sanitizedDTO = new UpdateUserDTO(
            $dto->userID,
            $dto->erpUserID,
            $dto->name,
            $this->hashPassword($dto->password),
            $dto->access
        );

        $user = $this->repo->updateUser($sanitizedDTO);

        return $this->toUserDTO($user);
    }

    public function deleteUser(int $userID, UserDTO $authenticatedUser): bool
    {
        if (!$authenticatedUser->access->hasAccessLevel(UserRole::ADMINISTRATOR)) {
            throw new AccessLevelException();
        }

        return $this->repo->deleteUser($userID);
    }

    public function getAuthData(string $username): AuthUserDTO
    {
        $user = $this->repo->getUserByUsername($username);

        if(!$user) { throw new UserNotFoundException(); }

        return new AuthUserDTO(
            $user->getID(),
            $user->getUsername(),
            $user->getPassword()
        );
    }

    private function hashPassword(?string $password): ?string
    {
        if (is_null($password)) return null;

        return password_hash($password, PASSWORD_DEFAULT);
    }
}
