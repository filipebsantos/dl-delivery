<?php

namespace DLDelivery\Infrastructure\Http;

use Psr\Container\ContainerInterface;
use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Interface\Http\Middleware\AuthMiddleware;
use DLDelivery\Exception\RouterInvalidControllerException;
use DLDelivery\Exception\RouterInvalidControllerMethodException;
use DLDelivery\Exception\RouterMethodNotSupportedException;
use DLDelivery\Exception\RouterPathDoNotExistsException;
use DLDelivery\Exception\RouterUnauthorizedException;

class Router
{
    private array $routes = [];

    public function __construct(private LoggerInterface $logger, private ContainerInterface $container) {}

    public function loadRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);
        $method = strtoupper($method);
        $publicPaths = ['/', '/login'];

        if (in_array($path, $publicPaths)) {
            $user = null;
        } else {
            $authMiddleware = $this->container->get(AuthMiddleware::class);
            
            $user = $authMiddleware->handle();

            if ($user === null) {
                throw new RouterUnauthorizedException;
                return;
            }
        }

        if (!isset($this->routes[$method])) {
            throw new RouterMethodNotSupportedException;
            return;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            /**
             * Change values between {} for regex capture group
             * Ex: /user/{id} -> /user/([^/]+)
             */
            $pattern = preg_replace('#\{([^}]+)\}#', '([^/]+)', $route);

            // Add delimiters and anchors to match the full path exactly
            $pattern = "#^{$pattern}$#";

            /**
             * Try to match the current path with the pattern
             * 
             * Ex: /user/101 == /user/([^/]+)
             *     $matches = [
             *                  0 => '/user/101',
             *                  1 => '101
             *                ]
             */
            if (preg_match($pattern, $path, $matches)) {
                // Remove the full match (index 0)
                array_shift($matches);
                
                /**
                 * Extract paramaters names from the original route pattern
                 *
                 * Ex: /user/{id} => $paramNames = [ 0 => 'id' ] 
                 */
                preg_match_all('#\{([^}]+)\}#', $route, $paramNames);
                
                /**
                 * Combine parameter names with matched values into an associative array
                 * 
                 * Ex: ['id' => 101]
                 */
                $params = array_combine($paramNames[1], $matches);

                // Add authenticated user
                if (!is_null($user)){
                    $params['authUser'] = $user;
                }

                $this->executeHandler($handler, $params);
                return;
            }
        }
        
        $this->logger->warning(
            "Someone try to access an invalid URI",
            [
                "IP" => $_SERVER['REMOTE_ADDR'],
                "USER_AGENT" => $_SERVER['HTTP_USER_AGENT'],
                "URI" => "$method $path"
            ]
            );
        throw new RouterPathDoNotExistsException;
    }

    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);
        return '/' . trim(preg_replace('#^/api#', '', $path), '/');
    }

    private function executeHandler(array $handler, array $params = []): void
    {
        [$controllerClass, $methodName] = $handler;

        if (!class_exists($controllerClass)) {
            $this->logger->warning(
                "Someone try to access an invalid controller",
                [
                    "IP" => $_SERVER['REMOTE_ADDR'],
                    "USER_AGENT" => $_SERVER['HTTP_USER_AGENT'],
                    "CONTROLLER" => $controllerClass
                ]
                );
            throw new RouterInvalidControllerException;
            return;
        }

        $controller = $this->container->get($controllerClass);

        if (!method_exists($controller, $methodName)) {
            $this->logger->warning(
                "Someone try to access an invalid controller method",
                [
                    "IP" => $_SERVER['REMOTE_ADDR'],
                    "USER_AGENT" => $_SERVER['HTTP_USER_AGENT'],
                    "METHOD" => "$controllerClass::$controller"
                ]
                );
            throw new RouterInvalidControllerMethodException;
            return;
        }

        call_user_func_array([$controller, $methodName], $params);
    }
}
