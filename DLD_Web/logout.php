<?php
    include_once("globals.php");
    include_once("models/Messages.php");

    $message = new Message($BASE_URL);

    unset($_SESSION["activeUser"]);
    if(isset($_COOKIE["_dld-SsID"])){
        setcookie("_dld-SsID", "", time() - 3600, "/");
    }

    $message->setMessage("Você saiu com sucesso", "success");