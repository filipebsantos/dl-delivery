<?php

namespace DLDelivery\Application\DTO\Client;

use JsonSerializable;

class LocationUpdateDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $latitude,
        public readonly ?string $longitude,
        public readonly ?int $neighborhoodID,
        public readonly ?string $housePicture = null
    ){}
}