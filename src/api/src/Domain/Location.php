<?php

namespace DLDelivery\Domain;

use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Exception\Client\InvalidLocationCoordinate;
use ReturnTypeWillChange;

class Location
{
    private ?int $id;
    private string $latitude;
    private string $longitude;
    private int $neighborhoodID;
    private ?string $neighborhood;
    private ?string $housePicture = null;

    public function __construct(string $latitude, string $longitude, int $neighbohoodID, ?int $locationID = null, ?string $housePictureFile = null)
    {
        $lat = $this->normalizeCoordinate($latitude);
        $lon = $this->normalizeCoordinate($longitude);
        
        if (!$this->isValidLatitude($lat) || !$this->isValidLongitude($lon)) {
            throw new InvalidLocationCoordinate;
        }

        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->neighborhoodID = $neighbohoodID;
        $this->id = $locationID;
        $this->housePicture = $housePictureFile;
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setNeighborhoodName(string $name): void
    {
        $this->neighborhood = $name;
    }

    public function hasHousePicture(): bool
    {
        return !is_null($this->housePicture);
    }

    public function toResponseDTO(): LocationResponseDTO
    {
        return new LocationResponseDTO(
            $this->latitude,
            $this->longitude,
            $this->neighborhood,
            $this->id,
            $this->housePicture
        );
    }

    private function isValidLatitude(string $latitude): bool
    {
        if (!is_numeric($latitude)) return false;
        $floatLatitude = floatval($latitude);
        return $floatLatitude >= -90 && $floatLatitude <= 90;
    }

    private function isValidLongitude(string $longitude): bool
    {
        if (!is_numeric($longitude)) return false;
        $floatLongitude = floatval($longitude);
        return $floatLongitude >= -180 && $floatLongitude <= 180;
    }

    private function normalizeCoordinate(string $coordinate): string
    {
        return str_replace(",", ".", $coordinate);
    }
}