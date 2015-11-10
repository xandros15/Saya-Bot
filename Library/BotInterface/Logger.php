<?php

namespace Library\BotInterface;

interface Logger
{

    public static function add($message, $type);

    public function setLogger($filename, $path, $timezone);
}