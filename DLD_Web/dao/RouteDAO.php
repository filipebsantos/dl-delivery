<?php
/* Data Access Objects for User's class */
include(__DIR__ . "/../models/Route.php");

class RouteDAO implements RouteInterface
{

    private $dbConn;

    public function __construct(PDO $dbConn)
    {
        $this->dbConn = $dbConn;
    }

    public function addNewRoute(Route $route)
    {

        // Check is already existis an pending route for the deliveryman
        $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM routes WHERE deliveryman = :deliveryman AND status = 'PENDENTE'");
        $stmt->bindValue(":deliveryman", $route->getDeliveryman(), PDO::PARAM_INT);

        try {

            $stmt->execute();
            $numRecords = $stmt->fetch();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }

        if ($numRecords[""] == 0) {
            $stmt = $this->dbConn->prepare("INSERT INTO routes (deliveryman, [user]) VALUES (:deliveryman, :user)");
            $stmt->bindValue(":deliveryman", $route->getDeliveryman(), PDO::PARAM_INT);
            $stmt->bindValue(":user", $route->getCreateByUSer(), PDO::PARAM_INT);

            try {
                return $stmt->execute();
            } catch (PDOException $pdoError) {

                throw new Exception($pdoError->getMessage());
            }
        } else {

            throw new Exception("Já existe uma rota em aberto para esse entregador.");
        }
    }

    public function listRoutes($dateFilter = null, bool $onlyUnfinished = true, int $deliveryman = 0)
    {

        if (!is_null($dateFilter)) {
            $date = date("d-m-Y");

            $checkDate = date_create($dateFilter);

            if ($checkDate !== false) {
                $date = date_format($checkDate, "d-m-Y");
            } else {
                throw new Exception("Data inválida");
                exit;
            }
        }

        switch ($onlyUnfinished) {

            case true:
                $sqlQuery = "SELECT routes.id,
                                        CONCAT(users.firstname, ' ', users.lastname) AS fullname,
                                        routes.starttime,
                                        routes.endtime,
                                        routes.status,
                                        routes.datecreation
                                FROM routes
                                LEFT JOIN users
                                ON routes.deliveryman = users.id
                                WHERE (status = 'PENDENTE' OR status = 'INICIADA')" .
                    ($deliveryman == 0 ? "" : " AND routes.deliveryman = :deliverymanid") .
                    (!isset($date) ? "" : " AND DATETRUNC(day, datecreation) = :date");
                break;

            case false:
                $sqlQuery = "SELECT routes.id,
                                        CONCAT(users.firstname, ' ', users.lastname) AS fullname,
                                        routes.starttime,
                                        routes.endtime,
                                        routes.status,
                                        routes.datecreation
                                FROM routes
                                LEFT JOIN users
                                ON routes.deliveryman = users.id
                                WHERE DATETRUNC(day, datecreation) = :date"  . ($deliveryman == 0 ? "" : " AND routes.deliveryman = :deliverymanid");
                break;
        }

        $stmt = $this->dbConn->prepare($sqlQuery);
        if (isset($date)) {
            $stmt->bindValue(":date", $date);
        }
        if ($deliveryman <> 0) {
            $stmt->bindValue(":deliverymanid", $deliveryman, PDO::PARAM_INT);
        }

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }

    public function getRoute(int $routeid)
    {

        $stmt = $this->dbConn->prepare("SELECT routes.id,
                                                   routes.deliveryman,
                                                   CONCAT(deliverer.firstname, ' ', deliverer.lastname) AS deliverymanName,
                                                   routes.[user],
                                                   CONCAT(creator.firstname, ' ', creator.lastname) AS createBy,
                                                   routes.starttime,
                                                   routes.endtime,
                                                   routes.datecreation,
                                                   routes.status
                                            FROM routes
                                            JOIN users AS deliverer ON routes.deliveryman = deliverer.id
                                            JOIN users AS creator ON routes.[user] = creator.id
                                            WHERE routes.id = :id");
        $stmt->bindValue(":id", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }
    }

    public function getRouteLocations(int $routeid)
    {

        $stmt = $this->dbConn->prepare("SELECT routes_locations.id,
                                                   routes_locations.routeid,
                                                   routes_locations.locationid,
                                                   locations.latitude,
                                                   locations.longitude,
                                                   locations.housepicture,
                                                   locations.type,
                                                   locations.neighborhood,
                                                   locations.obs,
                                                   clients.name AS clientName
                                            FROM routes_locations
                                            LEFT JOIN locations ON routes_locations.locationid = locations.id
                                            LEFT JOIN clients ON locations.clientid = clients.id
                                            WHERE routes_locations.routeid = :routeid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }
    }

    public function addRouteLocation(int $routeid, int $locationid)
    {

        //Check if route status is not finished
        $stmt = $this->dbConn->prepare("SELECT status FROM routes WHERE id = :routeid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $returnQuery = $stmt->fetch();

            if ($returnQuery["status"] === "FINALIZADA") {
                throw new Exception("Rota já finalizada! Inclusão não permitida.");
                exit;
            }
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }

        //Check if selected location is already in route
        $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM routes_locations WHERE locationid = :locationid AND routeid = :routeid");
        $stmt->bindValue(":locationid", $locationid, PDO::PARAM_INT);
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $numRecords = $stmt->fetchColumn();

            if ($numRecords > 0) {
                throw new Exception("Essa localização já foi adicionada a rota " . $routeid);
                exit;
            }
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }

        // Add location to route
        $stmt = $this->dbConn->prepare("INSERT INTO routes_locations (routeid, locationid) VALUES (:routeid, :locationid)");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
        $stmt->bindValue(":locationid", $locationid, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }

    public function deleteRoute(int $routeid)
    {

        $stmt = $this->dbConn->prepare("DELETE FROM routes WHERE id = :id");
        $stmt->bindValue(":id", $routeid, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }
    }

    public function deleteRouteLocation(int $routeid, int $routeLocationId)
    {

        $stmt = $this->dbConn->prepare("DELETE FROM routes_locations WHERE id = :routeLocationId AND routeid = :routeid");
        $stmt->bindValue(":routeLocationId", $routeLocationId, PDO::PARAM_INT);
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }
    }

    public function changeRouteStatus(int $routeid, string $status)
    {

        switch ($status) {
            case "INICIADA":
                $dateTime = "starttime";
                break;

            case "FINALIZADA":
                $dateTime = "endtime";
                break;
        }

        //Check if already exists an started route
        if ($status ==  "INICIADA") {
            $stmtCheck = $this->dbConn->prepare("SELECT COUNT(*) FROM routes WHERE deliveryman = (SELECT deliveryman FROM routes WHERE id = :routeid) AND status = 'INICIADA'");
            $stmtCheck->bindValue(":routeid", $routeid, PDO::PARAM_INT);
            $stmtCheck->execute();
            $count = $stmtCheck->fetchColumn();

            // Se já existe uma rota INICIADA para o mesmo 'deliveryman', lançar uma exceção
            if ($count > 0) {
                throw new Exception("Já existe uma rota iniciada. Finalize a rota antes de inciar uma nova.");
            }
        }

        $stmt = $this->dbConn->prepare("UPDATE routes SET status = :status, $dateTime = :datetime WHERE id = :routeid");
        $stmt->bindValue(":status", $status);
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
        $stmt->bindValue(":datetime", date("d-m-Y H:i:s"));

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {

            throw new Exception($pdoError->getMessage());
        }
    }

    public function addClientToRoute(int $routeid, int $clientid)
    {

        //Check if route status is not finished
        $stmt = $this->dbConn->prepare("SELECT status FROM routes WHERE id = :routeid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $returnQuery = $stmt->fetch();

            if ($returnQuery["status"] === "FINALIZADA") {

                throw new Exception("Rota já finalizada! Inclusão não permitida");
                exit;
            }
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }

        // Check if client is already add to route
        $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM routes_clients WHERE routeid = :routeid AND clientid = :clientid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
        $stmt->bindValue(":clientid", $clientid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $numRecords = $stmt->fetchColumn();

            if ($numRecords == 0) {

                // Add client to route
                $stmt = $this->dbConn->prepare("INSERT INTO routes_clients (routeid, clientid) VALUES (:routeid, :clientid)");
                $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
                $stmt->bindValue(":clientid", $clientid, PDO::PARAM_INT);

                try {
                    return $stmt->execute();
                } catch (PDOException $pdoError) {
                    throw new Exception($pdoError->getMessage());
                }
            } else {
                throw new Exception("Este cliente já foi adicionado a essa rota");
                exit;
            }
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }

    public function listRouteClients(int $routeid)
    {
        $stmt = $this->dbConn->prepare("SELECT routes_clients.clientid,
                                               clients.id,
                                               clients.name,
                                               routes_clients.phonenumber,
                                               routes_clients.status,
                                               CASE 
                                                   WHEN EXISTS (
                                                       SELECT 1
                                                       FROM locations
                                                       WHERE locations.clientid = clients.id
                                                         AND locations.type = 'RESIDENCIA'
                                                   ) 
                                                   THEN 1
                                                   ELSE 0
                                               END AS has_residence
                                        FROM routes_clients
                                        JOIN clients ON routes_clients.clientid = clients.id
                                        LEFT JOIN locations ON clients.id = locations.clientid
                                        WHERE routes_clients.routeid = :routeid
                                        GROUP BY routes_clients.clientid, clients.id, clients.name, routes_clients.phonenumber, routes_clients.status");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $returnQuery = $stmt->fetchAll();
            return $returnQuery;
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }


    public function deleteRouteClient(int $routeid, int $clientid)
    {
        $stmt = $this->dbConn->prepare("DELETE FROM routes_clients WHERE routeid = :routeid AND clientid = :clientid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
        $stmt->bindValue(":clientid", $clientid, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }

    public function changeRouteClientStatus(int $routeid, int $clientid, int $status)
    {
        // Check if routes is started
        $stmt = $this->dbConn->prepare("SELECT status FROM routes WHERE id = :routeid");
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $queryResult = $stmt->fetch();

            if ($queryResult["status"] != "INICIADA") {
                throw new Exception("Rota com status inválido");
                exit;
            }
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }

        // Check if alrady existis another client in same route with delivery status in progress before update another record
        if ($status == 1) {
            $stmt = $this->dbConn->prepare("SELECT COUNT(*) FROM routes_clients WHERE routeid = :routeid AND status = 1");
            $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);
            try {
                $stmt->execute();
                $queryResult = $stmt->fetchColumn();

                if ($queryResult > 0) {
                    throw new Exception("Já existe um cliente com entrega em andamento");
                    exit;
                }
            } catch (PDOException $pdoError) {
                throw new Exception($pdoError->getMessage());
            }
        }

        // Update client route status
        $stmt = $this->dbConn->prepare("UPDATE routes_clients SET status = :status WHERE routeid = :routeid AND clientid = :clientid");
        $stmt->bindValue(":status", $status, PDO::PARAM_INT);
        $stmt->bindValue(":clientid", $clientid, PDO::PARAM_INT);
        $stmt->bindValue(":routeid", $routeid, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $pdoError) {
            throw new Exception($pdoError->getMessage());
        }
    }
}
