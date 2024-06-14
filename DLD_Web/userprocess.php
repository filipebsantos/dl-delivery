<?php
require_once("globals.php");
require_once("includes/db.php");
require_once("dao/UserDAO.php");
require_once("models/Messages.php");

// Accept only posts requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 
    if (isset($_POST["action"])) {

        // Instanciate the message service
        $message = new Message($BASE_URL);

        // Handle actions
        switch ($_POST["action"]) {

                // Create new user
            case "create":

                $Username    = filter_input(INPUT_POST, "txtUserName");
                $FirstName   = filter_input(INPUT_POST, "txtFirstname");
                $LastName    = filter_input(INPUT_POST, "txtLastname");
                $Role        = filter_input(INPUT_POST, "optRole", FILTER_SANITIZE_NUMBER_INT);
                $Password1   = filter_input(INPUT_POST, "txtPassword1");
                $Password2   = filter_input(INPUT_POST, "txtPassword2");
                $phoneNumber = filter_input(INPUT_POST, "txtPhoneNumber");

                if (isset($_POST["optUserActive"])) {
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
                    $user->setPhoneNumber($phoneNumber);

                    if ($Password1 === $Password2) {

                        $user->setPassword($Password1);
                    } else {

                        $message->setMessage("As senhas não são iguais.", "danger", "back");
                    }

                    $createUser = new UserDAO($dbConn);

                    try {
                        if ($createUser->createUser($user)) {

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

                if (isset($_POST["userId"]) && isset($_POST["txtFirstname"]) && isset($_POST["txtLastname"]) && isset($_POST["optRole"])) {
                    $userID = filter_input(INPUT_POST, "userId", FILTER_SANITIZE_NUMBER_INT);
                    $firstName = filter_input(INPUT_POST, "txtFirstname");
                    $lastName = filter_input(INPUT_POST, "txtLastname");
                    $userRole = filter_input(INPUT_POST, "optRole", FILTER_SANITIZE_NUMBER_INT);
                    $phoneNumber = filter_input(INPUT_POST, "txtPhoneNumber");

                    $user = new User();
                    $updateUser = new UserDAO($dbConn);

                    $user->setId($userID);
                    $user->setFirstname($firstName);
                    $user->setLastname($lastName);
                    $user->setRole($userRole);
                    $user->setPhoneNumber($phoneNumber);

                    if (isset($_POST["optUserActive"])) {
                        $user->setUserActive(1);
                    } else {
                        $user->setUserActive(0);
                    }

                    // Grab the user to be updated data for security checks
                    $targetUser = new UserDAO($dbConn);
                    $targetUserData = $targetUser->getUser($userID);                    

                    // Avoid update a user with a high access level
                    if ($targetUserData["role"] > $_SESSION["activeUser"]["role"]) {
                        $message->setMessage("Você não tem nível de acesso para alterar esse usuário.", "danger", "back");
                        exit;
                    }

                    // Avoid user change his own access level
                    if ($targetUserData["id"] == $_SESSION["activeUser"]["id"] && $user->getRole() != $_SESSION["activeUser"]["role"]) {
                        $message->setMessage("Você não pode alterar seu próprio nível de acesso.", "danger", "back");
                        exit;
                    }

                    // If passwords were provided try to update
                    if (!empty($_POST["txtPassword1"]) && !empty($_POST["txtPassword2"])) {

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

                    try {
                        if ($updateUser->updateUser($user)) {

                            $message->setMessage("Usuário atualizado.", "success", "edituser.php?id=" . $user->getId());
                        } else {

                            $message->setMessage("Erro ao atualizar cadastro.", "danger", "edituser.php?id=" . $user->getId());
                        }
                    } catch (Exception $error) {

                        $message->setMessage($error->getMessage(), "danger", "edituser.php?id=" . $user->getId());
                    }
                } else {
                    $message->setMessage("Faltam campos obrigatórios.", "danger", "back");
                }

                break;

                // Delete user record
            case "delete":

                if (isset($_POST["userId"])) {
                    $postClientId = filter_input(INPUT_POST, "userId", FILTER_SANITIZE_NUMBER_INT);
                    $deleteUser = new UserDAO($dbConn);

                    // Check if trying delete self account
                    if ($postClientId == intval($_SESSION["activeUser"]["id"])) {
                        $message->setMessage("Você não pode excluir o próprio cadastro.", "danger", "user.php");
                        exit;
                    }

                    // Get user data
                    try {
                        $userData = $deleteUser->getUser($postClientId);
                    } catch (Exception $error) {
                        $message->setMessage($error->getMessage(), "warning", "back");
                    }

                    // Check if it is trying to delete a higher level then logged user
                    if (intval($userData["role"]) >= $_SESSION["activeUser"]["role"]) {
                        $message->setMessage("Você não ter permissão para excluir o usuário.", "danger", "user.php");
                        exit;
                    }

                    try {
                        $deleteUser->delUser($postClientId);
                        $message->setMessage("Usuário excluído com sucesso!", "success", "user.php");
                    } catch (Exception $error) {

                        $message->setMessage($error->getMessage(), "warning", "user.php");
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
