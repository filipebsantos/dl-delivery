<?php
session_start();

// Define app name and version
define("APP_NAME", getenv("APP_NAME"));
define("APP_VERSION", getenv("APP_VERSION"));

// Default timezone
date_default_timezone_set(getenv('TZ'));

// Database connection
define("DB_HOST", getenv("DB_HOST"));
define("DB_PORT", getenv("DB_PORT"));
define("DB_NAME", getenv("DB_NAME"));
define("DB_USER", getenv("DB_USER"));
define("DB_PASS", getenv("DB_PASS"));

// Define HTTP protocol
if (!is_null(getenv("HTTP_PROTOCOL")) && !empty(getenv("HTTP_PROTOCOL"))){
    $http_procotol = getenv("HTTP_PROTOCOL");
} else {
    $http_procotol = "http";
}

//Define HTTP listening port
if (!is_null(getenv("HTTP_SERVER_PORT")) && !empty(getenv("HTTP_SERVER_PORT"))){
    $http_port = getenv("HTTP_SERVER_PORT");
}


if (isset($http_port)){
    $BASE_URL = $http_procotol . "://" . $_SERVER["SERVER_NAME"] . ":" . $http_port . "/";
} else {
    $BASE_URL = $http_procotol . "://" . $_SERVER["SERVER_NAME"] . "/";  
}
?>