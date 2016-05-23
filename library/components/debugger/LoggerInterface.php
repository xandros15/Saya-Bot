<?php

namespace library\BotInterface;

interface LoggerInterface
{

    public static function add($message, $type);

    public static function setLogger($filename, $path, $timezone);
}