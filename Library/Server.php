<?php

namespace Library;

use Library\Chatter\Textline;
use Library\Chatter\MessageRelay;
use Library\Connection\Socket;
use Library\Helper\ServerHelper;

class Server implements BotInterface\ServerController
{
    const NUMBER_OF_RECONNECTS = 10;

    private $name;
    private $host;
    private $ports;
    private $password;
    private $textline;
    private $connection;
    private $messageRelay;

    public function __construct()
    {
        $this->messageRelay = new MessageRelay();
        $this->textline = new Textline($this->messageRelay);
        $this->connection = new Socket();
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setPorts($ports)
    {
        $this->ports = $ports;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPorts()
    {
        return $this->ports;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTextline()
    {
        return $this->textline;
    }

    public function sendData($data)
    {
        return $this->connection->sendData($data . IRC_EOL);
    }

    public function connect()
    {
        $ports = ServerHelper::parsePorts($this->getPorts());
        $nrOfPorts = count($ports);
        $portKey = 0;
        $try = self::NUMBER_OF_RECONNECTS;
        while (($try--)) {
            $port = $ports[$portKey];
            $this->connection->connect($this->getHost(), $port);
            if ($this->connection->isConnected()) {
                return true;
            }
            if(++$portKey > ($nrOfPorts - 1)){
                $portKey = 0;
            }
            sleep(1 + self::NUMBER_OF_RECONNECTS - $try);
        }
        //TODO add error
        return false;
    }

    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    public function disconnect($force = false)
    {
        $this->connection->disconnect((bool) $force);
        return $this;
    }

    public function loadData()
    {
        $data = $this->connection->getData();
        if (!$data) {
            return false;
        }

        $signsToDelete = [chr(9), chr(10), chr(11), chr(13), chr(0)];
        $message = trim(str_replace($signsToDelete, '', $data));

        if (!$message) {
            return false;
        }
        $this->messageRelay->setMessage($message);
        return $this->textline->update();
    }
}
