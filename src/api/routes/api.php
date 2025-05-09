<?php

use DLDelivery\Application\AuthApplicationService;
use DLDelivery\Infrastructure\Http\ResponseHelper;
use DLDelivery\Interface\Http\ClientController;
use DLDelivery\Interface\Http\UserController;

return [
    'GET' => [
        '/' => [ResponseHelper::class, 'info'],
        // UserController
        '/user' => [UserController::class, 'listUsers'],
        '/user/{id}' => [UserController::class, 'fetchUser'],
        // ClientController
        '/client' => [ClientController::class, 'list'],
        '/client/{id}' => [ClientController::class, 'getByID'],
        '/client/location/{id}' => [ClientController::class, 'getLocation'],
        // RouteController
        
    ],
    'POST' => [
        '/login' => [AuthApplicationService::class, 'loginUser'],
        // UserController
        '/user' => [UserController::class, 'createUser'],
        // ClientController
        '/client' => [ClientController::class, 'createClient'],
        '/client/{id}/location' => [ClientController::class, 'createLocation'],
        // RouteController
        
    ],
    'PUT' => [
        // UserController
        '/user/{id}' => [UserController::class, 'updateUser'],
        // ClientController
        '/client/{id}' => [ClientController::class, 'update'],
        // RouteController
        
    ],
    'DELETE' => [
        // UserController
        '/user/{id}' => [UserController::class, 'deleteUser'],
        // ClientController
        '/client/{id}' => [ClientController::class, 'delete'],
        // RouteController
        
    ],
];