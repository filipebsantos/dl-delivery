<?php

    /*This class implements the client's locations.*/

    class Location {
        
        private $id;
        private $clientId;
        private $typeLocation;
        private $latitude;
        private $longitude;
        private $pictureURL;
        private $neighborhood;
        private $observations;

        public function __construct(string $clientId) {
            $this->clientId = $clientId;
        }

        public function setLocationId(int $id) {
            $this->id = $id;
        }

        public function getLocationId() {
            return $this->id;
        }

        public function setLocationClient(int $clientid) {
            $this->id = $clientid;
        }

        public function getLocationClient() {
            return $this->clientId;
        }

        public function setLocationType(string $type) {
            $this->typeLocation = $type;
        }

        public function getLocationType() {
            return $this->typeLocation;
        }

        public function setCoordinates(string $latitude, string $longitude) {
            $this->latitude = $latitude;
            $this->longitude = $longitude;
        }

        public function getCoordinates() {
            return [
                "lat" => $this->latitude,
                "lon" => $this->longitude
            ];
        }

        public function setHousePicture(string $filename) {
            $this->pictureURL = $filename;
        }

        public function getHousePicture() {
            return $this->pictureURL;
        }

        public function setLocationNeighborhood(string $neihnborhood) {
            $this->neighborhood = $neihnborhood;
        }

        public function getLocationNeighborhood() {
            return $this->neighborhood;
        }

        public function setLocationObservation(string $text) {
            $this->observations = $text;
        }

        public function getLocationObservation() {
            return $this->observations;
        }

    }

    interface LocationInterface {
        // List all client's locations
        public function listLocations(int $clientId);

        // Save new location
        public function newLocation(Location $loc);

        // Update client's location
        public function updateLocation(Location $loc);

        // Delete location
        public function deleteLocation(int $locationId);

        // Get location
        public function getLocation(int $locationId);
    }