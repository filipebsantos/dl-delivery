<?php

namespace DLDelivery\Interface\Http\Middleware;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Application\JwtService;
use DLDelivery\Application\DTO\User\UserDTO;
use DLDelivery\Exception\User\InvalidTokenException;

class AuthMiddleware
{
    public function __construct(private JwtService $jwtService, private LoggerInterface $logger) {}

    public function handle(): ?UserDTO
    {
        $headers = getallheaders();

        $authorizationHeader = $headers['Authorization'] ?? null;
        if (empty($authorizationHeader)) {
            $this->logger->debug("Authorization Header not set");
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $token = $matches[1];
        } else {
            $this->logger->debug("Malformed token structure");
            return null;
        }

        try {
            return $this->jwtService->validate($token);
        } catch (InvalidTokenException $e) {
            $this->logger->debug("Can't validate token");
            return null;
        }
    }
}
