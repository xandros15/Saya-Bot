<?php

namespace library\botInterface;

interface ServerController
{

    public function connect();

    public function disconnect();

    public function isConnected();

    public function loadData();
}