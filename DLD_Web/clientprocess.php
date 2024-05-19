<?php
    require_once("globals.php");
    require_once("includes/db.php");
    require_once("dao/ClientDAO.php");
    require_once("models/Messages.php");

    // Accept only posts requests
    if ($_SERVER["REQUEST_METHOD"] === "POST"){

        // 
        if(isset($_POST["action"])) {

            // Instanciate the message service
            $message = new Message($BASE_URL);

            // Handle actions
            switch ($_POST["action"]) {

                // Create new client
                case "create":
                    
                    if ((isset($_POST["txtClientId"]) && !empty($_POST["txtClientId"])) && (isset($_POST["txtClientName"]) && !empty($_POST["txtClientName"]))) {
                        
                        $clientId = filter_input(INPUT_POST, "txtClientId");
                        $clientName = filter_input(INPUT_POST, "txtClientName");
                        
                        $client = new Client();

                        $client->setClientId(intval($clientId));
                        $client->setClientName(trim(strtoupper($clientName)));

                        $createClient = new ClientDAO($dbConn);

                        try{
                            if ($createClient->newClient($client)) {
                                $message->setMessage("Cliente cadastrado com sucesso!", "success", "back");
                            } else {
                                $message->setMessage("Erro ao tentar cadastrar o cliente.", "danger", "back");
                            }
                        } catch (Exception $error) {
                            $message->setMessage($error->getMessage(), "danger", "back");
                        }
                    
                    } else {
                        $message->setMessage("Preencha todos os campos.", "danger", "back");
                    }
                    
                    break;

                // Update client record
                case "update":
                    // Change this in future, get the client id from a session to avoid mapinutaltion through client side
                    
                    $id = filter_input(INPUT_POST, "clientId");
                    $clientName = filter_input(INPUT_POST, "txtClientName");
                    
                    $client = new Client();
                    $client->setClientId($id);
                    $client->setClientName(trim(strtoupper($clientName)));

                    $updateClient = new ClientDAO($dbConn);

                    try {
                        if($updateClient->updateClient($client)){
                            $message->setMessage("Cliente atualizado!", "success", "editclient.php?id=" . $id);
                        } else {
                            $message->setMessage("Não foi possível atualizar o cadastro.", "danger", "editclient.php?id=" . $id);
                        }
                        
                    } catch (Exception $error) {
                        $message->setMessage($error->getMessage(), "danger", "editclient.php?id=" . $id);
                    }
                    break;
                
                // Delete client record
                case "delete":
                    if (isset($_POST["clientId"]) && !empty($_POST["clientId"])) {
                        $delClient = new ClientDAO($dbConn);

                        try{
                            $delClient->deleteClient($_POST["clientId"]);
                            $message->setMessage("Cliente excluído com sucesso!", "success", "client.php");
                        } catch (Exception $error) {
                            $message->setMessage($error->getMessage(), "danger", "client.php");
                        }
                    }

                    break;
                
                // Filter client search
                case "filter" :
                        
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