<?php
session_start();

// Default timezone
date_default_timezone_set(getenv('TZ'));

// Database connection
define("DB_HOST", getenv("DB_HOST"));
define("DB_PORT", getenv("DB_PORT"));
define("DB_NAME", getenv("DB_NAME"));
define("DB_USER", getenv("DB_USER"));
define("DB_PASS", getenv("DB_PASS"));

$APPNAME = "DL Delivery";
$APPVERSION = "1.0-rc";

$BASE_URL = "http://" . $_SERVER["SERVER_NAME"] . "/";
?>