<?php

namespace DLDelivery\Interface\Validator;

use DLDelivery\Application\DTO\User\CreateUserDTO;
use DLDelivery\Application\DTO\User\UpdateUserDTO;
use DLDelivery\Application\DTO\User\UserListFilterDTO;
use DLDelivery\Domain\Enum\UserRole;
use DLDelivery\Exception\InvalidJsonException;
use DLDelivery\Exception\User\MissingCreateUserPayloadException;
use DLDelivery\Exception\User\MissingUpdateUserPayloadException;

class UserRequestValidator
{
    public static function list(): UserListFilterDTO
    {
        $page = (int) (filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT) ?: 1);
        $perPage = (int) (filter_input(INPUT_GET, 'perPage', FILTER_SANITIZE_NUMBER_INT) ?: 10);
        $filters = isset($_GET['filters']) ? (array) $_GET['filters'] : [];

        return new UserListFilterDTO(
            $page,
            $perPage,
            null,
            $filters
        );
    }

    public static function create(): CreateUserDTO
    {
        $rawData = json_decode(file_get_contents("php://input"), true);
        
        if (!is_array($rawData)) {
            throw new InvalidJsonException;
        }

        $name = filter_var($rawData['name'], FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
        $username = filter_var($rawData['username'], FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
        $password = filter_var($rawData['password']) ?? null;
        $access = (int) (filter_var($rawData['access']) ?: 2); // By default user will be an operator 
        $erpUserID = filter_var($rawData['erpUserID'], FILTER_SANITIZE_NUMBER_INT) ?? null;

        if (is_null($name) || is_null($username) || is_null($password)) {
            throw new MissingCreateUserPayloadException;
        }

        return new CreateUserDTO(
            $name,
            $username,
            $password,
            UserRole::tryFrom($access),
            $erpUserID
        );
    }

    public static function update(int $id): UpdateUserDTO
    {
        $rawData = json_decode(file_get_contents("php://input"), true);

        if (!is_array($rawData)) {
            throw new InvalidJsonException;
        }
        
        $erpUserID = isset($rawData['erpUserID']) ? filter_var($rawData['erpUserID'], FILTER_SANITIZE_NUMBER_INT) : null;
        $name = isset($rawData['name']) ? filter_var($rawData['name'], FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $password = isset($rawData['password']) ? filter_var($rawData['password']) : null;
        $access = isset($rawData['access']) ? filter_var($rawData['access'], FILTER_SANITIZE_NUMBER_INT) : null;      

        if (is_null($id)) {
            throw new MissingUpdateUserPayloadException;
        }

        return new UpdateUserDTO(
            $id,
            $erpUserID,
            $name,
            $password,
            is_numeric($access) ? UserRole::tryFrom($access) : null
        );
    }
}