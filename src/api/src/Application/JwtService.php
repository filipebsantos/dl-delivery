<?php

namespace DLDelivery\Application;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Application\DTO\JwtDTO;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Exception\User\InvalidTokenException;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret, private LoggerInterface $logger)
    {
        $this->jwtSecret = $jwtSecret;
    }

    public function generate(JwtDTO $dto): string {
        return JWT::encode($dto->toArray(), $this->jwtSecret, 'HS256');
    }

    public function validate(string $token): UserDTO
    {
        try {
            $token = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $jwtDTO = JwtDTO::fromStdClass($token);

            return new UserDTO(
                $jwtDTO->sub,
                $jwtDTO->erpUserID,
                $jwtDTO->name,
                $jwtDTO->username,
                $jwtDTO->role
            );
            
        } catch (Exception $ex) {
            $this->logger->debug($ex->getMessage());
            throw new InvalidTokenException();
        }
    }
}