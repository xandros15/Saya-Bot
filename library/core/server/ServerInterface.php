<?php

namespace Saya\Core\Server;

interface ServerInterface
{
    public function connect();

    public function disconnect();

    public function isConnected();
}