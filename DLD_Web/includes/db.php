<?php

    try{
        $dbConn = new PDO("sqlsrv:server=". DB_HOST .";Database=". DB_NAME .";Encrypt=no", DB_USER, DB_PASS);
        $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $pdoError) {
        echo $pdoError->getMessage();
        exit;
    }