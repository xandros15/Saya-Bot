<?php

namespace Library\BotInterface;

interface ServerController
{

    public function connect();

    public function disconnect();

    public function isConnected();

    public function loadData();
}