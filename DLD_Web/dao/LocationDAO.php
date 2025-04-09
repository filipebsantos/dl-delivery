<?php
    include(__DIR__ . "/../models/Location.php");

    class LocationDAO implements LocationInterface {
        
        private $dbConn;

        public function __construct(PDO $connection) {
            $this->dbConn = $connection;
        }

        public function listLocations(int $clientId) {
            $stmt = $this->dbConn->prepare("SELECT * FROM locations WHERE clientid = :clientid");
            $stmt->bindValue(":clientid", $clientId);

            try{
                
                $stmt->execute();
                $queryResult = $stmt->fetchAll();
                return $queryResult;
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function newLocation(Location $loc) {

            $coordinate = $loc->getCoordinates();

            // Locations type "RESIDENCIA" and "TRABALHO" are unique, so, there is allowed just one record for "RESIDENCIA" and "TRABALHO" per client.
            if ($loc->getLocationType() == "RESIDENCIA" || $loc->getLocationType() == "TRABALHO") {
                $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM locations WHERE clientid = :clientid AND type = :type");
                $stmt->bindValue(":clientid", $loc->getLocationClient(), PDO::PARAM_INT);
                $stmt->bindValue(":type", $loc->getLocationType());

                try{
                    $stmt->execute();
                    $numRecords = $stmt->fetchColumn();

                    if ($numRecords == 1) {
                        throw new Exception("Já existe uma localização cadastrada como ". $loc->getLocationType());
                    }
                } catch (PDOException $pdoError) {
                    throw new Exception($pdoError->getMessage());
                }
            }
            
            $stmt = $this->dbConn->prepare("INSERT INTO locations (clientid, latitude, longitude, neighborhood, housepicture, obs, type) VALUES (:clientid, :latitude, :longitude, :neighborhood, :housepicture, :obs, :type)");
            $stmt->bindValue(":clientid", $loc->getLocationClient(), PDO::PARAM_INT);
            $stmt->bindValue(":latitude", $coordinate["lat"]);
            $stmt->bindValue(":longitude", $coordinate["lon"]);
            $stmt->bindValue(":neighborhood", $loc->getLocationNeighborhood());
            $stmt->bindValue(":housepicture", $loc->getHousePicture());
            $stmt->bindValue(":obs", $loc->getLocationObservation());
            $stmt->bindValue(":type", $loc->getLocationType());

            try{
                $stmt->execute();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function updateLocation(Location $loc) {
            
            $coordinate = $loc->getCoordinates();
            
            $stmt = $this->dbConn->prepare("UPDATE locations SET latitude = :latitude, longitude = :longitude, neighborhood = :neighborhood, housepicture = :housepicture, obs = :obs, type = :type WHERE id = :id");
            $stmt->bindValue(":id", $loc->getLocationId(), PDO::PARAM_INT);
            $stmt->bindValue(":latitude", $coordinate["lat"]);
            $stmt->bindValue(":longitude", $coordinate["lon"]);
            $stmt->bindValue(":neighborhood", $loc->getLocationNeighborhood());
            $stmt->bindValue(":housepicture", $loc->getHousePicture());
            $stmt->bindValue(":obs", $loc->getLocationObservation());
            $stmt->bindValue(":type", $loc->getLocationType());

            try{
                $stmt->execute();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        public function deleteLocation(int $locationId) {
            if (isset($locationId) && !empty($locationId)) {
                $stmt = $this->dbConn->prepare("DELETE FROM locations WHERE id = :id");
                $stmt->bindValue(":id", $locationId);
                
                try{
                    
                    return $stmt->execute();
                } catch (PDOException $pdoError) {
                    
                    throw new Exception($pdoError->getMessage());
                }
            }
        }

        public function getLocation(int $locationId) {

            $stmt = $this->dbConn->prepare("SELECT locations.id, 
                                                   clients.id as clientid,
                                                   clients.name, 
                                                   locations.latitude, 
                                                   locations.longitude, 
                                                   locations.neighborhood, 
                                                   locations.housepicture, 
                                                   locations.obs, 
                                                   locations.type 
                                            FROM locations 
                                            INNER JOIN clients ON locations.clientid = clients.id 
                                            WHERE locations.id = :locationId");
                                            
            $stmt->bindValue(":locationId", $locationId, PDO::PARAM_INT);

            try{
                $stmt->execute();
                return $stmt->fetch();
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }
    }