<?php
    require_once(__DIR__ . "/globals.php");
    require_once(__DIR__ . "/models/Messages.php");

    $message = new Message($BASE_URL);
    
    $returnMessage = $message->getMessage();

    if (isset($_COOKIE["_dld-SsID"]) && !isset($_SESSION["activeUser"])) {
        
        require_once("includes/db.php");
        require_once("dao/UserDAO.php");

        $login = new UserDAO($dbConn);
        $user = $login->loginByToken($_COOKIE["_dld-SsID"]);

        if ($user != false) {

            // Create login session
            $sessionUser = [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "role" => $user->getRole()
            ];
            $_SESSION["activeUser"] = $sessionUser;

            //Check if access is from mobile
            $isMobile = str_contains(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile");

            if($isMobile){
                header("Location: " . $BASE_URL . "delivery/home.php");
            } else {
                header("Location: " . $BASE_URL . "dashboard.php");
            }
        } else {
            $returnMessage = [
                "msg" => "Sua sessão expirou. Faça o login novamente.",
                "type" => "danger"
            ];
        }
    } elseif (isset($_SESSION["activeUser"])) {
        //Check if access is from mobile
        $isMobile = str_contains(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile");

        if($isMobile){
            header("Location: " . $BASE_URL . "delivery/home.php");
        } else {
            header("Location: " . $BASE_URL . "dashboard.php");
        }
    }

    if (!empty($returnMessage)) {
        $message->clearMessage();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $APPNAME ?></title>
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/style.css">
    <link rel="shortcut icon" href="<?= $BASE_URL ?>imgs/favicon-dld.png" type="image/png">
</head>
<body>
    <header>
        <nav class="navbar" id="navigation-bar">
            <div class="container-fluid">
                <div id="nav-logo">
                    <img src="<?= $BASE_URL ?>imgs/favicon-dld.png" alt="DL Delivery WEB" class="navbar-brand">
                    <span class="navbar-brand"><?= $APPNAME ?></span>
                </div>
            </div>
        </nav>
    </header>

    <?php if (!empty($returnMessage["msg"])) : ?>
        <div class="container mt-3" id="alert-box">
            <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                <?= $returnMessage["msg"] ?>
            </div>
        </div>
    <?php endif; ?>

<div class="container mt-5">

    <div class="container border rounded shadow" id="login-box">
        <img src="<?= $BASE_URL ?>imgs/favicon-dld.png" id="img-logo" alt="Logomarca DL Delivery">
        <form action="<?= $BASE_URL ?>login.php" method="post">
            <input type="hidden" name="action" value="login">

            <div class="form-floating mt-3 mb-3">
                <input type="text" class="form-control" id="txtUser" name="txtUser" placeholder="Usuário" required>
                <label for="txtUser">Usuário</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Senha" required>
                <label for="txtPassword">Senha</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="optRememberMe" id="optRememberMe" checked>
                <label for="optRememberMe" class="form-check-label">Lembrar de mim</label>
            </div>

            <input type="submit" class="btn btn-primary btn-lg center-submit" value="Login">
        </form>
    </div>
</div>
<?php include(__DIR__ . "/includes/footer.php"); ?>