<?php

namespace DLDelivery\Application\DTO\Client;

use JsonSerializable;

class LocationDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
        public readonly int $neighborhoodID,
        public readonly ?int $id = null,
        public readonly ?string $housePicture = null
    ){}

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'neighborhood' => $this->neighborhoodID,
            'housePicture' => $this->housePicture
        ];
    }
}