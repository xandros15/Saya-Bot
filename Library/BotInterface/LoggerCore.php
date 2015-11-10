<?php

namespace Library\BotInterface;

interface LoggerCore
{

    public function getPrefix($type);

    public function flatten(array $message);

    public function save($message);
}