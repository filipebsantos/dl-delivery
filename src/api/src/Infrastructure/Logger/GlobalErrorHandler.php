<?php

namespace DLDelivery\Infrastructure\Logger;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Exception\ExceptionHandler;
use DLDelivery\Infrastructure\Logger\TrackIDProvider;

class GlobalErrorHandler
{
    private static LoggerInterface $logger;
    private static bool $handledError = false;

    public static function init(LoggerInterface $logger): void {
        self::$logger = $logger;

        ini_set('display_erros', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);

        set_error_handler([self::class, 'uncatchError']);
        set_exception_handler([self::class, 'uncatchException']);
        register_shutdown_function([self::class, 'shutdownHandler']);
    }

    public static function uncatchException(\Throwable $e): void {
        (new ExceptionHandler(self::$logger))->handle($e);
        self::$handledError = true;
    }

    public static function uncatchError($errno, $errstr, $errfile, $errline): bool {
        self::$logger->critical(
            sprintf("[%s] %s", $errno, $errstr),
            [
                'file' => $errfile,
                'line' => $errline,
                'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
            ]
        );

        self::$handledError = true;
        return true;
    }

    public static function shutdownHandler(): void {
        $error = error_get_last();
    
        if ($error && !self::$handledError) {
            self::$logger->critical(
                "Shutdown Error: {$error['message']} (Type: {$error['type']})",
                [
                    'file' => $error['file'],
                    'line' => $error['line'],
                    'memory' => memory_get_usage(true)
                ]
            );
    
            if (in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
                http_response_code(500);
    
                if (ob_get_length()) {
                    ob_clean();
                }
    
                echo 'Fatal Internal Server Error - TrackID: ' . TrackIDProvider::get();
            }
        }
    
        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }
    
    // public static function shutdownHandler(): void {
    //     $error = error_get_last();
    //     if ($error && !self::$handledError) {
    //         self::$logger->critical(
    //             "Shutdown Error: {$error['message']} (Type: {$error['type']})",
    //             [
    //                 'file' => $error['file'],
    //                 'line' => $error['line'],
    //                 'memory' => memory_get_usage(true)
    //             ]
    //         );
    //     }
    // }
}