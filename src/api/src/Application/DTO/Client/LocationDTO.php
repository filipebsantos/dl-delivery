<?php

namespace DLDelivery\Application\DTO\Client;

use DLDelivery\Application\DTO\UploadImageDTO;

class LocationDTO
{
    public function __construct(
        public readonly string $latitude,
        public readonly string $longitude,
        public readonly int $neighborhoodID,
        public readonly ?UploadImageDTO $housePicture = null
    ){}
}