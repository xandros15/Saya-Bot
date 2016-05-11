<?php

namespace library\BotInterface;

interface Logger
{

    public static function add($message, $type);

    public static function setLogger($filename, $path, $timezone);
}