<?php

namespace Library;

use Library\Chatter\Chat;
use Library\Chatter\MessageRelay;

class Server implements BotInterface\Server
{
    public $name;
    public $dns;
    public $ports;
    public $password;
    
    public $chat;
    
    private $messageRelay;

    public function __construct()
    {
        $this->messageRelay = new MessageRelay();
        $this->chat = new Chat($this->messageRelay);
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

    public function loadData($message)
    {
        $this->messageRelay->setMessage($message);
        return $this->chat->update();
    }
}
