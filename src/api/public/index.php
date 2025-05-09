<?php

require __DIR__ . "/../vendor/autoload.php";

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Infrastructure\Http\Router;
use DLDelivery\Infrastructure\Logger\GlobalErrorHandler;
use DLDelivery\Infrastructure\Logger\TrackIDProvider;

TrackIDProvider::generate();

$container = require __DIR__ . '/../src/Infrastructure/Core/Container.php';
$routeList = require __DIR__ . "/../routes/api.php";

$fileLogger = $container->get(LoggerInterface::class);
GlobalErrorHandler::init($fileLogger);

header('Content-Type: application/json; charset=utf-8');

$router = $container->get(Router::class);
$router->loadRoutes($routeList);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
