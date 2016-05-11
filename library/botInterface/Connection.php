<?php

namespace library\BotInterface;

interface Connection
{
    public function getHost();

    public function getPort();

    public function connect($server, $port);

    public function disconnect();

    public function isConnected();

    public function sendData($data);

    public function getData();
}
