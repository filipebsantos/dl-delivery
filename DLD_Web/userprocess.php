<?php
    require_once("globals.php");
    require_once("includes/db.php");
    require_once("dao/UserDAO.php");
    require_once("models/Messages.php");

    // Accept only posts requests
    if ($_SERVER["REQUEST_METHOD"] === "POST"){

        // 
        if(isset($_POST["action"])) {

            // Instanciate the message service
            $message = new Message($BASE_URL);

            // Handle actions
            switch ($_POST["action"]) {

                // Create new user
                case "create":

                    $Username  = filter_input(INPUT_POST, "txtUserName");
                    $FirstName = filter_input(INPUT_POST, "txtFirstname");
                    $LastName  = filter_input(INPUT_POST, "txtLastname");
                    $Role      = filter_input(INPUT_POST, "optRole");
                    $Password1 = filter_input(INPUT_POST, "txtPassword1");
                    $Password2 = filter_input(INPUT_POST, "txtPassword2");
                    
                    if(isset($_POST["optUserActive"])) {
                        $activeUser = 1;
                    } else {
                        $activeUser = 0;
                    }

                    if ($Username && $FirstName && $LastName && $Role && $Password1 && $Password2) {

                        $user = new User();

                        $user->setUsername($Username);
                        $user->setFirstname($FirstName);
                        $user->setLastname($LastName);
                        $user->setRole($Role);
                        $user->setUserActive($activeUser);

                        if ($Password1 === $Password2) {

                            $user->setPassword($Password1);
                        } else {
                    
                            $message->setMessage("As senhas não são iguais.", "danger", "back");
                        }

                        $createUser = new UserDAO($dbConn);

                        try{
                            if($createUser->createUser($user)) {

                                $message->setMessage("Cadastro realizado.", "success", "back");
                            } else {

                                $message->setMessage("Erro ao salvar cadastro.", "danger", "back");
                            }
                        } catch (Exception $error) {

                            $message->setMessage($error->getMessage(), "danger", "back");
                        }
                    } else {

                        $message->setMessage("Informar todos os campos.", "danger", "back");
                    }

                    break;

                // Update user record
                case "update":

                    $userID = filter_input(INPUT_POST, "userId");
                    $firstName = filter_input(INPUT_POST, "txtFirstname");
                    $lastName = filter_input(INPUT_POST, "txtLastname");
                    $userRole = filter_input(INPUT_POST, "optRole");

                    $user = new User();
                    $updateUser = new UserDAO($dbConn);
                    
                    $user->setId($userID);
                    $user->setFirstname($firstName);
                    $user->setLastname($lastName);
                    $user->setRole($userRole);
                    
                    if(isset($_POST["optUserActive"])) {
                        $user->setUserActive(1);
                    } else {
                        $user->setUserActive(0);
                    }

                    // If passwords were provided try to update
                    if (!empty($_POST["txtPassword1"]) && !empty($_POST["txtPassword2"])){

                        if ($_POST["txtPassword1"] == $_POST["txtPassword2"]) {
                            try {
                                $updateUser->updatePassword($_POST["txtPassword1"], $user->getId());
                            } catch (Exception $error) {
                                $message->setMessage($error->getMessage(), "danger", "edituser.php?id=" . $user->getId());
                            }
                        } else {
                            $message->setMessage("As senhas não conferem.", "danger", "edituser.php?id=" . $user->getId());
                            exit;
                        }
                    }

                    try{
                        if ($updateUser->updateUser($user)) {

                            $message->setMessage("Usuário atualizado.", "success", "edituser.php?id=" . $user->getId());
                        } else {       

                            $message->setMessage("Erro ao atualizar cadastro.", "danger", "edituser.php?id=" . $user->getId());
                        }
                    } catch (Exception $error) {

                        $message->setMessage($error->getMessage(), "danger", "edituser.php?id=" . $user->getId());
                    }
                    
                    break;
                
                // Delete user record
                case "delete":

                    if (isset($_POST["userId"])){
                        $deleteUser = new UserDAO($dbConn);

                        try{

                            $deleteUser->delUser($_POST["userId"]);
                            $message->setMessage("Usuário excluído com sucesso!", "success", "deluser.php");
                        } catch (Exception $error) {

                            $message->setMessage($error->getMessage(), "success", "deluser.php");
                        }
                    } else {
                        header("Location: " . $BASE_URL . "inicio.php");
                    }

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