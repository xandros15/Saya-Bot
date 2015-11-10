<?php

namespace Library\Debugger;

class Logger extends \Library\Debugger\Core implements \Library\BotInterface\Logger
{
    const
        ERROR = 0,
        WARRNING = 1,
        INFO = 3,
        SUCCESS = 4;

    private static
    /**
     * @var \Library\Debugger\Core
     */
        $logger;

    public static function add($message, $type = self::INFO)
    {

        $core   = static::$logger;
        $prefix = $core->getPrefix($type);
        if (is_array($message)) {
            $message = $core->flatten($message, $prefix);
        } elseif (is_string($message)) {
            $message = $prefix . trim($message) . PHP_EOL;
        } else {
            $message = $core->getPrefix(self::ERROR) . 'Message must be a string or array. Is ' . gettype($message) . PHP_EOL;
        }
        $core->save($message);
        return $message;
    }

    public function setLogger($filename, $dirname, $timezone = 'UTC')
    {
        if (isset(self::$logger)) {
            return self::$logger;
        }
        $core         = new Core();
        $core->setDatetime($timezone)
            ->setDirname($dirname)
            ->setFilename($filename);
        return self::$logger = $core;
    }
}