<?php

namespace Library;

use Library\Chatter\Textline;
use Library\Chatter\MessageRelay;
use Library\Connection\Socket;
use Library\Helper\ServerHelper;
use Library\Debugger\Logger;

class Server implements BotInterface\ServerController
{
    /** @var int */
    public $maxReconnects = 10;

    /** @var string */
    private $name;

    /** @var string */
    private $host;

    /** @var string */
    private $ports;

    /** @var string */
    private $password;

    /** @var Textline */
    private $textline;

    /** @var Socket */
    private $connection;

    /** @var MessageRelay */
    private $messageRelay;

    public function __construct()
    {
        $this->messageRelay = new MessageRelay();
        $this->textline     = new Textline($this->messageRelay);
        $this->connection   = new Socket();
    }

    /**
     * set name of server
     *
     * @param type $name
     * @return \Library\Server
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set host of server
     *
     * @param type $host
     * @return \Library\Server
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * set ports of server connections
     *
     * @param type $ports
     * @return \Library\Server
     */
    public function setPorts($ports)
    {
        $this->ports = $ports;
        return $this;
    }

    /**
     * return name of server
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * return host of server
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * return ports of server connections
     *
     * @return string
     */
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * return server password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * return Textline object
     *
     * @return Textline
     */
    public function getTextline()
    {
        return $this->textline;
    }

    /**
     * Sending data to server. Return number of sent bytes
     *
     * @param string $data
     * @return int
     */
    public function sendData($data)
    {
        return $this->connection->sendData($data . IRC_EOL);
    }

    /**
     * connect to server
     *
     * @return boolean
     */
    public function connect()
    {
        if ($this->connection->isConnected()) {
            $this->connection->disconnect();
        }

        if (!$this->getPorts() || !$this->getHost()) {
            throw new Exception("There isn't set port or host");
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

        Logger::add("Can't connect to {$this->host}:{$this->ports}", Logger::ERROR);

        return false;
    }

    /**
     * checking if bot is connected to server
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    /**
     * disconnect from server
     *
     * @return Server
     */
    public function disconnect()
    {
        $this->connection->disconnect();
        return $this;
    }

    /**
     * load data to textline
     *
     * @return boolean
     */
    public function loadData()
    {
        $data = $this->connection->getData();
        if (!$data) {
            return false;
        }

        $message = trim(str_replace([chr(9), chr(10), chr(11), chr(13), chr(0)], '', $data));

        if (!$message) {
            return false;
        }

        $this->messageRelay->setMessage($message);

        return $this->textline->update();
    }
}