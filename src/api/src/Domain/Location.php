<?php

namespace DLDelivery\Domain;

use DLDelivery\Application\DTO\Client\LocationDTO;

class Location
{
    private ?int $id;
    private string $latitude;
    private string $longitude;
    private int $neighbohoodID;
    private ?string $housePicture;

    public function __construct(string $latitude, string $longitude, int $neighbohoodID, ?int $locationID = null, ?string $housePictureFile = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->neighbohoodID = $neighbohoodID;
        $this->id = $locationID;
        $this->housePicture = $housePictureFile;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function toDTO(): LocationDTO
    {
        return new LocationDTO(
            $this->latitude,
            $this->longitude,
            $this->neighbohoodID,
            $this->id,
            $this->housePicture
        );
    }
}