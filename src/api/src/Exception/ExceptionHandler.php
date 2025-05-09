<?php

namespace DLDelivery\Exception;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Exception\Client\ClientAlreadyExistsException;
use DLDelivery\Exception\Client\ClientNotFoundException;
use DLDelivery\Exception\Client\LocationNotFoundException;
use DLDelivery\Exception\Client\MissingCreateClientPayloadException;
use DLDelivery\Exception\User\AccessLevelException;
use DLDelivery\Exception\User\InvalidPasswordException;
use DLDelivery\Exception\User\InvalidTokenException;
use DLDelivery\Exception\User\MissingCreateUserPayloadException;
use DLDelivery\Exception\User\UserAlreadyExistsException;
use DLDelivery\Exception\User\UserNotFoundException;
use DLDelivery\Infrastructure\Http\ResponseHelper;
use DLDelivery\Infrastructure\Logger\TrackIDProvider;

class ExceptionHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function handle(\Throwable $exception): void
    {
        switch (true) {
            case $exception instanceof ClientNotFoundException:
            case $exception instanceof LocationNotFoundException:
            case $exception instanceof UserNotFoundException:
                $this->respond(404, $exception->getMessage(), $exception->getCode());
                break;
            
            case $exception instanceof RouterUnauthorizedException:
            case $exception instanceof InvalidPasswordException:
            case $exception instanceof InvalidTokenException:
                $this->respond(401, $exception->getMessage(), $exception->getCode());
                break;

            case $exception instanceof AccessLevelException:
                $this->respond(403, $exception->getMessage(), $exception->getCode());
                break;  
            
            case $exception instanceof RouterPathDoNotExistsException:
            case $exception instanceof RouterInvalidControllerException:
            case $exception instanceof RouterMethodNotSupportedException:
            case $exception instanceof RouterInvalidControllerMethodException:
            case $exception instanceof UserAlreadyExistsException:
            case $exception instanceof MissingCreateUserPayloadException:
            case $exception instanceof MissingCreateClientPayloadException:
            case $exception instanceof ClientAlreadyExistsException;
                $this->respond(400, $exception->getMessage(), $exception->getCode());
                break;

            default:
                $this->logger->error(
                    $exception->getMessage(),
                    [
                        "file" => $exception->getFile(),
                        "line" => $exception->getLine(),
                        "trace" => $exception->getTrace()
                    ]
                    );
                $this->respond(500, "Internal Server Error", 500);
        }
    }

    private function respond(int $status, string $message, ?int $erroCode): void
    {
        ResponseHelper::send([
            "message" => $message,
            "code" => $erroCode,
            "TrackID" => TrackIDProvider::get()
        ], $status);
    }
}
