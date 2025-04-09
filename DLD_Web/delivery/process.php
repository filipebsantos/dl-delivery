<?php
    require_once(__DIR__ . "/../includes/db.php");
    require_once(__DIR__ . "/../dao/ClientDAO.php");
    require_once(__DIR__ . "/../dao/RouteDAO.php");
    require_once(__DIR__ . "/../dao/LocationDAO.php");

    $clientDAO = new ClientDAO($dbConn);

    // Accept only posts requests
    if ($_SERVER["REQUEST_METHOD"] === "POST"){

        // 
        if(isset($_POST["action"])) {

            // Instanciate the message service
            $message = new Message($BASE_URL);

            // Handle actions
            switch ($_POST["action"]) {

                case "":
                    
                    break;
                
                default:

                    http_response_code(401);
                    header('Content-Type: application/json');
                    echo json_encode(["status" => 401, "message" => "Invalid action."]);                    
                    
                    break;
            }
        } else {

            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(["status" => 401, "message" => "Action not set."]);
        }

    } else {

        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["status" => 401, "message" => "Method not allowed."]);
    }