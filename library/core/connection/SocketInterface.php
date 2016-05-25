<?php

namespace Saya\Core\Connection;

interface SocketInterface
{
    public function getHost();

    public function getPort();

    public function connect($host, $port);

    public function disconnect();

    public function isConnected();

    public function sendData($data);

    public function getData();
}
