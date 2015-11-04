<?php

namespace Library\BotInterface;

interface ServerController
{

    public function connect();

    public function disconnect();
    
    public function isConnected();
    
    public function loadData();

    public function setName($name);

    public function setHost($address);

    public function setPorts($ports);

    public function getName();

    public function getHost();

    public function getPorts();

    public function getPassword();

    public function getTextline();
}
