<?php

use DI\ContainerBuilder;
use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Application\JwtService;
use DLDelivery\Domain\Interface\ClientRepositoryInterface;
use DLDelivery\Domain\Interface\UserRepositoryInterface;
use DLDelivery\Infrastructure\Logger\FileLogger;
use DLDelivery\Infrastructure\Persistence\SqliteUserRepository;
use DLDelivery\Infrastructure\Http\Router;
use DLDelivery\Infrastructure\Persistence\SqliteClientRepository;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../config/');
$dotenv->load();

$builder = new ContainerBuilder();

$builder->addDefinitions([
    // Logger
    LoggerInterface::class => \DI\autowire(FileLogger::class),

    // Database connection
    PDO::class => function () {
        $dbEngine = $_ENV['DB_ENGINE'] ?? 'mssql';

        if ($dbEngine === 'sqlite') {
            $pathDB = __DIR__ . '/../../../database/database.sqlite';

            $pdo = new PDO('sqlite:' . $pathDB);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON;');

            return $pdo;
        }

        // Implementar MS SQL no futuro
    },

    // User Repository
    UserRepositoryInterface::class => function (PDO $pdo) {
        $dbEngine = $_ENV['DB_ENGINE'] ?? 'sqlite';

        if ($dbEngine === 'sqlite') {
            return new SqliteUserRepository($pdo);
        }

        // Implementar MS SQL no futuro
    },

    // Client Repository
    ClientRepositoryInterface::class => function ( PDO $pdo ) {
        $dbEngine = $_ENV['DB_ENGINE'] ?? 'sqlite';

        if ($dbEngine === 'sqlite') {
            return new SqliteClientRepository($pdo);
        }
    },

    Router::class => \DI\autowire(Router::class),
    JwtService::class => \DI\autowire()->constructorParameter('jwtSecret', $_ENV['JWT_SECRET'])
]);

return $builder->build();