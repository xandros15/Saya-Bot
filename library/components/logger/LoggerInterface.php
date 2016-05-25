<?php

namespace Saya\Components\Logger;

interface LoggerInterface
{
    const ERROR = 1;
    const WARNING = 2;
    const INFO = 3;
    const SUCCESS = 4;

    public static function add($message, $type);

    public static function setLogger($filename, $path, $timezone);
}