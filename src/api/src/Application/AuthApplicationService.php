<?php

namespace DLDelivery\Application;

use DLDelivery\Application\DTO\JwtDTO;
use DLDelivery\Domain\Service\UserService;
use DLDelivery\Exception\User\InvalidPasswordException;
use DLDelivery\Infrastructure\Http\ResponseHelper;

class AuthApplicationService
{

    public function __construct(private UserService $service, private JwtService $jwt) {}

    public function loginUser(): void
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;

        if (!$username || !$password) {
            return;
        }

        $userLogin = $this->service->getAuthData($username);

        if (!password_verify($password, $userLogin->password))
        {
            throw new InvalidPasswordException();
        }

        $user = $this->service->getUserByUsername($userLogin->username);
        
        $tokenIssuedAt = time();
        $tokenExpireAt = $tokenIssuedAt + 3600;

        $jwtDTO = new JwtDTO(
            $user->userID,
            $user->erpUserID,
            $user->name,
            $user->username,
            $user->access,
            $tokenIssuedAt,
            $tokenExpireAt
        );

        $userToken = $this->jwt->generate($jwtDTO);
        ResponseHelper::send(['token' => $userToken, 'issued' => strval($tokenIssuedAt), 'expires' => strval($tokenExpireAt)], 200);

    }
}