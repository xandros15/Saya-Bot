<?php

namespace Saya\Core\Connection;

interface ServerInterface
{
    public function connect();

    public function disconnect();

    public function isConnected();
}