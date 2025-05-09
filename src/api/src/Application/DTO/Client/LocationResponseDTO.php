<?php

namespace DLDelivery\Application\DTO\Client;

use JsonSerializable;

class LocationResponseDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
        public readonly string $neighborhood,
        public readonly int $id,
        public readonly ?string $housePicture = null
    ){}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'neighborhood' => $this->neighborhood,
            'housePicture' => $this->housePicture
        ];
    }
}