<?php

namespace DLDelivery\Infrastructure\Persistence;

use DLDelivery\Application\DTO\Client\ClientDTO;
use DLDelivery\Application\DTO\Client\ClientFilterDTO;
use DLDelivery\Application\DTO\Client\LocationDTO;
use DLDelivery\Application\DTO\Client\LocationResponseDTO;
use DLDelivery\Application\DTO\Client\LocationUpdateDTO;
use DLDelivery\Domain\Client;
use DLDelivery\Domain\Interface\ClientRepositoryInterface;
use DLDelivery\Domain\Location;
use DLDelivery\Exception\Client\ClientAlreadyExistsException;
use DLDelivery\Exception\Client\ClientNotFoundException;
use DLDelivery\Exception\Client\LocationNotFoundException;
use PDO;

class SqliteClientRepository implements ClientRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function create(ClientDTO $dto): Client
    {
        $stmt = $this->pdo->query("SELECT * FROM clients WHERE id = :id OR name = :name");
        $stmt->bindValue(':id', $dto->id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $dto->name);
        $stmt->execute();

        $returnDb = $stmt->fetch();
        if ($returnDb) {
            throw new ClientAlreadyExistsException;
        }

        $stmt = $this->pdo->prepare("INSERT INTO clients (id, name) VALUES (:id, :name)");
        $stmt->bindValue(':id', $dto->id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $dto->name);
        $stmt->execute();

        return new Client(
            $dto->id,
            $dto->name
        );
    }

    public function list(ClientFilterDTO $dto): array
    {
        $baseSql = "FROM clients";
        $params = [];
    
        if (!empty($dto->filters)) {
            $clauses = [];
            foreach ($dto->filters as $key => $value) {
                $clauses[] = "$key LIKE :$key";
                $params[$key] = "%$value%";
            }
            $baseSql .= " WHERE " . implode(' AND ', $clauses);
        }
    
        $countSql = "SELECT COUNT(*) as total " . $baseSql;
        $countStmt = $this->pdo->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue(":$key", $val, PDO::PARAM_STR);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();
    
        $listSql = "SELECT * " . $baseSql . " LIMIT :limit OFFSET :offset";
        $listStmt = $this->pdo->prepare($listSql);
        foreach ($params as $key => $val) {
            $listStmt->bindValue(":$key", $val, PDO::PARAM_STR);
        }
        $listStmt->bindValue(':limit', $dto->perPage, PDO::PARAM_INT);
        $listStmt->bindValue(':offset', ($dto->page - 1) * $dto->perPage, PDO::PARAM_INT);
        $listStmt->execute();
        $rows = $listStmt->fetchAll(PDO::FETCH_ASSOC);
    
        $clients = [];
        foreach ($rows as $row) {
            $clients[] = new Client($row['id'], $row['name']);
        }
    
        return [
            "page" => $dto->page,
            "perPage" => $dto->perPage,
            "totalPages" => (int)ceil($total / $dto->perPage),
            "totalItems" => $total,
            "clients" => $clients
        ];
    }
    
    public function getByID(int $clientID): ?Client
    {
        $stmt = $this->pdo->query("SELECT * FROM clients WHERE id = :id");
        $stmt->bindValue(':id', $clientID, PDO::PARAM_INT);
        $stmt->execute();

        $returnDb = $stmt->fetch();

        $locations = $this->getClientLocations($clientID);

        if (!$returnDb) {
            throw new ClientNotFoundException;
        }

        return new Client(
            $returnDb['id'],
            $returnDb['name'],
            $locations
        );
    }
    
    public function update(ClientDTO $dto): Client
    {
        $stmt = $this->pdo->prepare("UPDATE clients SET name = :name WHERE id = :id");
        $stmt->bindValue('id', $dto->id, PDO::PARAM_INT);
        $stmt->bindValue('name', $dto->name);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new ClientNotFoundException;
        }

        return $this->getByID($dto->id);
    }
    
    public function delete(int $clientID): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->bindValue('id', $clientID, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    private function getClientLocations(int $clientID): array
    {
        $stmt = $this->pdo->prepare("SELECT locations.id, locations.latitude, locations.longitude, locations.neighborhoodID, neighborhoods.name as neighborhood, locations.housePicture FROM locations
                                     LEFT JOIN neighborhoods ON locations.neighborhoodID = neighborhoods.id
                                     WHERE clientID = :clientID");
        $stmt->bindValue(":clientID", $clientID, PDO::PARAM_INT);
        $stmt->execute();

        $returnDB = $stmt->fetchAll();
        $locations = [];

        foreach ($returnDB as $row) {
            $loc = new Location($row['latitude'], $row['longitude'], $row['neighborhoodID'], $row['id'], $row['housePicture']);
            $loc->setNeighborhoodName($row['neighborhood']);
            $locations[] = $loc;
        }

        return $locations;
    }

    public function getLocationByID(int $locationID): Location
    {
        $stmt = $this->pdo->prepare("SELECT locations.id, locations.latitude, locations.longitude, locations.neighborhoodID, neighborhoods.name as neighborhood, locations.housePicture FROM locations
                                     LEFT JOIN neighborhoods ON locations.neighborhoodID = neighborhoods.id
                                     WHERE locations.id = :id");
        $stmt->bindValue(":id", $locationID, PDO::PARAM_INT);
        $stmt->execute();

        $returnDB = $stmt->fetch();

        if (!$returnDB) {
            throw new LocationNotFoundException;
        }

        $location = new Location(
            $returnDB['latitude'],
            $returnDB['longitude'],
            $returnDB['neighborhoodID'],
            $returnDB['id'],
            $returnDB['housePicture']
        );
        $location->setNeighborhoodName($returnDB['neighborhood']);

        return $location;
    }

    public function createLocation(int $clientID, LocationDTO $dto): Location
    {
        $stmt = $this->pdo->prepare("INSERT INTO locations (clientID, latitude, longitude, neighborhoodID, housePicture) VALUES (:clientID, :latitude, :longitude, :neighborhoodID, :housePicture)");
        $stmt->bindValue(":clientID", $clientID, PDO::PARAM_INT);
        $stmt->bindValue(":latitude", $dto->latitude);
        $stmt->bindValue(":longitude", $dto->longitude);
        $stmt->bindValue(":neighborhoodID", $dto->neighborhoodID, PDO::PARAM_INT);
        $stmt->bindValue(":housePicture", $dto->housePicture);
        $stmt->execute();

        $locationID = (int) $this->pdo->lastInsertId();
        
        return $this->getLocationByID($locationID);
    }

    public function updateLocation(LocationUpdateDTO $dto): Location
    {
        $fields = [];
        $params = [':id' => $dto->id];

        if (!is_null($dto->latitude)) {
            $fields[] = 'latitude = :latidude';
            $params[':latitude'] = $dto->latitude;
        }

        if (!is_null($dto->longitude)) {
            $fields[] = 'longitude = :latidude';
            $params[':longitude'] = $dto->longitude;
        }

        if (!is_null($dto->neighborhoodID)) {
            $fields[] = 'neighborhoodID = :neighborhoodID';
            $params[':neighborhoodID'] = $dto->neighborhoodID;
        }

        if (!is_null($dto->housePicture)) {
            $fields[] = 'housePicture = :housePicture';
            $params[':housePicture'] = $dto->housePicture;
        }

        if (empty($fields)) {
            return $this->getLocationByID($dto->id);
        }

        $sql = "UPDATE locations SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->getLocationByID($dto->id);
    }
        
    public function deleteLocation(int $locationID): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM locations WHERE id = :id");
        return $stmt->execute([':id' => $locationID]);
    }

}