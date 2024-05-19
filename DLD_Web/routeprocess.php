<?php
    require_once("globals.php");
    require_once("includes/db.php");
    require_once("dao/RouteDAO.php");
    require_once("models/Messages.php");

    $message = new Message($BASE_URL);
    $routeDAO = new RouteDAO($dbConn);

    // Accept only posts requests
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        if (isset($_POST["action"]) && filter_input(INPUT_POST, "action", FILTER_SANITIZE_SPECIAL_CHARS)) {
            $action = $_POST["action"];
            
            switch ($action) {
                case "addlocation":

                    if (isset($_POST["routeid"]) && isset($_POST["locationid"]) && isset($_POST["clientid"])) {
                        $routeid = filter_input(INPUT_POST, "routeid", FILTER_SANITIZE_NUMBER_INT);
                        $locationid = filter_input(INPUT_POST, "locationid", FILTER_SANITIZE_NUMBER_INT);
                        $clientid = filter_input(INPUT_POST, "clientid", FILTER_SANITIZE_NUMBER_INT);

                        if ($routeid != false && $locationid != false && $clientid != false) {
                            
                            try{
                                $routeDAO->addClientToRoute($routeid, $clientid);
                                $routeDAO->addRouteLocation($routeid, $locationid);
                                $message->setMessage("Localização adicionada a rota", "success", "back");
                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        }
                    }
                    
                    break;
                
                case "dellocation":
                    
                    if (isset($_POST["routeid"]) && isset($_POST["routelocationid"])) {

                        $routeid = filter_input(INPUT_POST, "routeid", FILTER_SANITIZE_NUMBER_INT);
                        $locationid = filter_input(INPUT_POST, "routelocationid", FILTER_SANITIZE_NUMBER_INT);

                        if ($routeid != false && $locationid != false) {

                            try{

                                $routeDAO->deleteRouteLocation($routeid, $locationid);
                                $message->setMessage("Localização removida a rota", "success", "back");
                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        }
                    } else {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                    }
                    break;
                
                case "updateRouteStatus":
                    
                    if (isset($_POST["routeid"]) && isset($_POST["routestatus"])) {
                        $routeid = filter_input(INPUT_POST, "routeid", FILTER_SANITIZE_NUMBER_INT);
                        $routeStatus = filter_input(INPUT_POST, "routestatus", FILTER_SANITIZE_SPECIAL_CHARS);

                        if ($routeid != false && $routeStatus != false && ($routeStatus == "INICIADA" || $routeStatus == "FINALIZADA")) {
                            try{
                                $routeDAO->changeRouteStatus(intval($routeid), $routeStatus);
                                $message->setMessage("Status da rota atualizado", "success", "back");
                            } catch (Exception $error) {
                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        } else {
                            $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                        }

                    } else {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                    }
                    break;
                
                case "create":

                    if (isset($_POST["optDeliveryman"]) && isset($_POST["createdBy"])) {
                        
                        $deliveryman = filter_input(INPUT_POST, "optDeliveryman", FILTER_SANITIZE_NUMBER_INT);
                        $user = filter_input(INPUT_POST, "createdBy", FILTER_SANITIZE_NUMBER_INT);

                        if ($deliveryman && $user) {
                            
                            $route = new Route();
                            $route->setDeliveryman($deliveryman);
                            $route->setCreateByUser($user);

                            try{
                                $routeDAO->addNewRoute($route);
                                $message->setMessage("Nova rota criada", "success", "back");

                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        }
                    }

                    break;

                case "delete":
                    
                    if (isset($_POST["routeId"]) && !empty($_POST["routeId"])) {
                        $delRoute = new RouteDAO($dbConn);

                        try{
                            $delRoute->deleteRoute($_POST["routeId"]);
                            $message->setMessage("Rota excluída com sucesso!", "success", "route.php");
                            
                        } catch (Exception $error) {
                            $message->setMessage($error->getMessage(), "danger", "route.php");
                        }
                    }
                    break;

                case "filter":

                    if (isset($_POST["optDeliveryman"], $_POST["txtDate"]) && $_POST["optDeliveryman"] !== "" && $_POST["txtDate"] !== "") {
                        $deliverymanId = filter_input(INPUT_POST, "optDeliveryman", FILTER_SANITIZE_NUMBER_INT);
                        $filterDate = filter_input(INPUT_POST, "txtDate", FILTER_SANITIZE_SPECIAL_CHARS);

                        $today = date_create();
                        $diff = date_diff(date_create($filterDate), $today);
                        
                        if ($diff->invert == 1){
                            $message->setMessage("A data não pode ser maior que o dia de hoje", "danger", "route.php");
                            exit;
                        }

                        if ($deliverymanId !== false && $filterDate !== false) {

                            try{
                                $filteredRoutes = $routeDAO->listRoutes($filterDate, false, $deliverymanId);
                                $_SESSION["filteredRoutes"] = $filteredRoutes;
                                header("Location:" . $BASE_URL . "route.php");
                            } catch (Exception $error) {
                                $message->setMessage($error->getMessage(), "danger", "route.php");
                            }
                            
                        } else {
                            $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                        }
                    } else {
                        $message->setMessage("Não foi possível processar sua requisição", "danger", "back");
                    }
                    break;
                
                case "addclient2route":

                    if (isset($_POST["routeid"]) && isset($_POST["clientid"])) {

                        $routeid = filter_input(INPUT_POST, "routeid", FILTER_SANITIZE_NUMBER_INT);
                        $clientid = filter_input(INPUT_POST, "clientid", FILTER_SANITIZE_NUMBER_INT);

                        if ($routeid != false && $clientid != false) {
                            
                            try{
                                $routeDAO->addClientToRoute($routeid, $clientid);
                                $message->setMessage("Cliente adicionado a rota", "success", "back");
                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        }
                    }

                    break;
                
                case "delclientroute":

                    if (isset($_POST["routeid"]) && isset($_POST["clientid"])) {

                        $routeid = filter_input(INPUT_POST, "routeid", FILTER_SANITIZE_NUMBER_INT);
                        $clientid = filter_input(INPUT_POST, "clientid", FILTER_SANITIZE_NUMBER_INT);

                        if ($routeid != false && $clientid != false) {
                            
                            try{
                                $routeDAO->deleteRouteClient($routeid, $clientid);
                                $message->setMessage("Cliente removido da rota", "success", "back");
                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }
                        }
                    }

                    break;
            }
        }
    } else {

        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["status" => 401, "message" => "Method not allowed."]);
    }

