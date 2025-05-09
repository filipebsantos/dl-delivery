<?php

namespace DLDelivery\Infrastructure\Logger;

use DLDelivery\Application\Contratcs\LoggerInterface;
use DLDelivery\Infrastructure\Logger\TrackIDProvider;

class FileLogger implements LoggerInterface
{
    private const LOG_DIR = __DIR__ . '/../../../logs/';

    public function debug(string $message, ?array $context = []): void
    {
        $this->log($message, 'DEBUG', $context);
    }

    public function info(string $message, ?array $context = []): void
    {
        $this->log($message, 'INFO', $context);
    }

    public function warning(string $message, ?array $context = []): void
    {
        $this->log($message, 'WARNING', $context);
    }

    public function error(string $message, ?array $context = []): void
    {
        $this->log($message, 'ERROR', $context);
    }

    public function critical(string $message, ?array $context = []): void
    {
        $this->log($message, 'CRITICAL', $context);
    }

    private function log(string $message, string $level, ?array $context = []): void {
        $trackID = TrackIDProvider::get() ?? 'NO_TRACKID';
        
        $backTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $callerClass = 'GLOBAL';
        $callerMethod = 'UNKNOWN';

        foreach ($backTrace as $frame) {
            if (isset($frame['class']) && $frame['class'] !== self::class) {
                $callerClass = $frame['class'];
                $callerMethod = $frame['function'] ?? 'UNKNOWN';
                break;
            }
        }

        $logContent = sprintf(
            "[%s][%s][TrackID %s](%s::%s) %s | Context: \n%s\n",
            date('Y-m-d H:i:s'),
            $level,
            $trackID,
            $callerClass,
            $callerMethod,
            $message,
            json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        $filename = self::LOG_DIR . "LOG_" . $level . ".log";

        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0777, true);
        }

        file_put_contents($filename, $logContent, FILE_APPEND);
    }
}