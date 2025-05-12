<?php

namespace DLDelivery\Application\DTO\Client;

use DLDelivery\Application\DTO\UploadImageDTO;

class LocationUpdateDTO
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $latitude = null,
        public readonly ?string $longitude = null,
        public readonly ?int $neighborhoodID = null,
        public readonly ?UploadImageDTO $housePicture = null
    ){}
}