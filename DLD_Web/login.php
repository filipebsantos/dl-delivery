<?php
    require_once("globals.php");
    require_once("includes/db.php");
    require_once("dao/UserDAO.php");
    require_once("models/Messages.php");

    // Accept only posts requests
    if ($_SERVER["REQUEST_METHOD"] === "POST"){

        // 
        if(isset($_POST["action"]) && $_POST["action"] == "login") {

            // Instanciate the message service
            $message = new Message($BASE_URL);

            if (isset($_POST["txtUser"]) && isset($_POST["txtPassword"])){
                $loginUser = new UserDAO($dbConn);

                try{
                    $username = filter_input(INPUT_POST, "txtUser");
                    $password = filter_input(INPUT_POST, "txtPassword");

                    $user = $loginUser->loginUser($_POST["txtUser"], $_POST["txtPassword"]);

                    if($user != false && $user->getUserActive() == 1) {

                        // Create login session
                        $sessionUser = [
                            "id" => $user->getId(),
                            "username" => $user->getUsername(),
                            "firstname" => $user->getFirstname(),
                            "lastname" => $user->getLastname(),
                            "role" => $user->getRole()
                        ];
                        $_SESSION["activeUser"] = $sessionUser;                                

                        // Create token and save cookie to remember me option
                        if (isset($_POST["optRememberMe"])){
                            
                            $token = bin2hex(random_bytes(16));
                            $expirationTimestamp = time() + (6 * 60 * 60);
                        
                            $expirationDate = new DateTime();
                            $expirationDate->setTimezone(new DateTimeZone("America/Fortaleza"));
                            $expirationDate->setTimestamp($expirationTimestamp);

                            $user->setToken($token);
                            $user->setTokenExpiration($expirationDate);

                            try{

                                $loginUser->saveToken($user);
                            } catch (Exception $error) {

                                $message->setMessage($error->getMessage(), "danger", "back");
                            }

                            // Save cookie with token for re-login
                            setcookie("_dld-SsID", $token, $expirationTimestamp);
                        }
                        
                        //Check if access is from mobile
                        $isMobile = str_contains(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile");

                        if($isMobile){
                            header("Location: " . $BASE_URL . "delivery/home.php");
                        } else {
                            header("Location: " . $BASE_URL . "dashboard.php");
                        }
                        
                    } elseif ($user != false && $user->getUserActive() == 0) {

                        $message->setMessage("Usuário inativo.", "danger", "back");
                    } else {

                        $message->setMessage("Senha inválida.", "danger", "back");
                    }
                } catch (Exception $error) {
                    $message->setMessage($error->getMessage(), "danger", "back");
                }
                
            } else {
                $message->setMessage("Dados de login não informado.", "danger", "back");
            }
        } else {

            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(["status" => 401, "message" => "What?"]);
        }

    } else {

        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(["status" => 401, "message" => "Get out of here!"]);
    }