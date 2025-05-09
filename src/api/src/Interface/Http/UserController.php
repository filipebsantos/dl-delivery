<?php

namespace DLDelivery\Interface\Http;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Application\UserApplicationService;
use DLDelivery\Exception\ExceptionHandler;
use DLDelivery\Infrastructure\Http\ResponseHelper;
use DLDelivery\Interface\Validator\UserRequestValidator;

class UserController
{
    public function __construct(private UserApplicationService $appService, private LoggerInterface $logger) {}

    public function listUsers(?UserDTO $authUser): void
    {
        try {
            $userListDTO = UserRequestValidator::list();
            $usersList = $this->appService->listAllUsers($userListDTO, $authUser);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        $users = [];
        foreach($usersList as $user) {
            $userArray = $user->toArray();
            unset($userArray['erpUserID'], $userArray['username'], $userArray['access']);
            $users[] = $userArray;
        }

        ResponseHelper::send($users, 200);
    }

    public function fetchUser(int $id, UserDTO $authUser): void
    {
        try {
            $user = $this->appService->getUserByID($id, $authUser);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        ResponseHelper::send($user->toArray(), 200);
    }

    public function createUser(?UserDTO $authUser): void
    {
        try {
            $userDTO = UserRequestValidator::create();
            $user = $this->appService->createUser($userDTO, $authUser);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        ResponseHelper::send($user->toArray(), 200);
    }

    public function deleteUser(int $id, UserDTO $authUser): void
    {
        try {
            $user = $this->appService->deleteUser($id, $authUser);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        if ($user) {
            ResponseHelper::send(["message" => "Ok"], 200);
        } else {
            ResponseHelper::send(["message" => "Can't delete user now"], 200);
        }
    }

    public function updateUser(int $id, UserDTO $authUser)
    {
        try {
            $userDTO = UserRequestValidator::update($id);
            $user = $this->appService->updateUser($userDTO, $authUser);
        } catch (\Throwable $th) {
            (new ExceptionHandler($this->logger))->handle($th);
            return;
        }

        ResponseHelper::send($user->toArray(), 200);
    }
}
