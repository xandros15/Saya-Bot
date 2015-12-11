<?php

namespace Library\Debugger;

use Library\Debugger\Core;
use Library\BotInterface\Logger;
use Exception;

class Logger extends Core implements Logger
{
    const
        ERROR = 1,
        WARRNING = 2,
        INFO = 3,
        SUCCESS = 4;

    public static function add($message, $type = self::INFO)
    {
        $core   = static::$logger;
        $prefix = $core->getPrefix($type);
        if (is_array($message)) {
            $message = $core->flatten($message, $prefix);
        } elseif (is_string($message)) {
            $message = $prefix . trim($message) . PHP_EOL;
        } else {
            throw new Exception('Message must be a string or array. Is ' . gettype($message));
        }
        $core->save($message);
        return $message;
    }
}