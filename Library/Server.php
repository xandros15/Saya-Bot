<?php

namespace Library;

class Server implements BotInterface\Server
{
    public $name;
    public $dns;
    public $ports;
    public $password;
    
    private $chat;

    public function __construct()
    {
        $this->chat = new Chat();
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setDns($dns)
    {
        $this->dns = $dns;
    }

    public function setPorts($ports)
    {
        $this->ports = $ports;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDns()
    {
        return $this->dns;
    }

    public function getPorts()
    {
        return $this->ports;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getChat()
    {
        return $this->chat;
    }

    public function connectToServer()
    {
        
    }

    public function disconnectFromServer()
    {
        
    }

    public function loadData($data)
    {
        return $this->chat->setIncommingData($data);
    }
}
