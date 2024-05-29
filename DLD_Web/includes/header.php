<?php
require_once(__DIR__ . "/../globals.php");
require_once(__DIR__ . "/../models/Messages.php");

$message = new Message($BASE_URL);

if (!isset($_SESSION["activeUser"])) {
    $message->setMessage("Faça login para acessar", "danger");
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/bootstrap.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>bootstrap-icons-1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= $BASE_URL ?>css/style.css">
    <script src="<?= $BASE_URL ?>js/jquery/jquery-3.7.1.min.js"></script>
    <link rel="shortcut icon" href="<?= $BASE_URL ?>imgs/favicon-dld.png" type="image/png">
</head>

<body>
    <header>
        <nav class="navbar" id="navigation-bar">
            <div class="container-fluid">
                <div id="nav-logo">
                    <img src="<?= $BASE_URL ?>imgs/favicon-dld.png" alt="DL Delivery WEB" class="navbar-brand">
                    <span class="navbar-brand"><?= APP_NAME ?></span>
                </div>
                <?php if (dirname($_SERVER["REQUEST_URI"]) == "/delivery") : ?>
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvaDeliveryMenu" aria-controls="offcanvaDeliveryMenu" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                <?php endif; ?>
            </div>
        </nav>
    </header>