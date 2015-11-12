<?php

namespace Library;

use Library\Chatter\Textline;
use Library\Chatter\MessageRelay;
use Library\Connection\Socket;
use Library\Helper\ServerHelper;

class Server implements BotInterface\ServerController
{
    public $maxReconnects = 10;

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
        if ($this->connection->isConnected()) {
            $this->connection->disconnect();
        }
        $ports = ServerHelper::parsePorts($this->getPorts());
        $try = $this->maxReconnects;
        while (($try--)) {
            $this->connection->connect($this->getHost(), current($ports));
            if ($this->connection->isConnected()) {
                return true;
            }
            if (next($ports) === false) {
                reset($ports);
            }
            sleep(1 + $this->maxReconnects - $try);
        }
        //TODO add error
        return false;
    }

    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    public function disconnect()
    {
        $this->connection->disconnect();
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
        (!DEBUG) or file_put_contents('debuger.log', date('[H:ia] ') . $message . PHP_EOL, FILE_APPEND);
        if (!$message) {
            return false;
        }
        $this->messageRelay->setMessage($message);
        return $this->textline->update();
    }
}
