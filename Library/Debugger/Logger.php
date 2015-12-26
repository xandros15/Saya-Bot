<?php

namespace Library\Debugger;

use Library\Debugger\Core;
use Library\BotInterface\Logger as LoggerInterface;
use Exception;

class Logger extends Core implements LoggerInterface
{

    public static function add($message, $type = self::INFO)
    {
        parent::add($message, $type);
        $prefix = self::$logger->getPrefix($type);
        if (is_array($message)) {
            $message = self::$logger->flatten($message, $prefix);
        } elseif (is_string($message)) {
            $message = $prefix . trim($message) . PHP_EOL;
        } else {
            throw new Exception('Message must be a string or array. Is ' . gettype($message));
        }
        self::$logger->save($message);
        return $message;
    }
}