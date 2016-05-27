<?php

namespace Saya\Core\Connection;

use Exception;
use Saya\Core\Input\Textline;
use Saya\Core\Input\Input;
use Saya\Components\Helper\ServerHelper;
use Saya\Components\Logger\Logger;
use Saya\Core\Input\Updater;
use Symfony\Component\Process\Exception\RuntimeException;

class Server implements ServerInterface, ServerInfo, Updater
{
    /** @var int */
    public $maxReconnects = 10;

    /** @var string */
    private $name;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $ports;

    /** @var string */
    private $password;

    /** @var Textline */
    private $textline;

    /** @var Socket */
    private $connection;

    public function __construct()
    {
        $this->textline = new Textline();
        $this->connection = new Socket();
    }

    /**
     * set name of server
     *
     * @param string $name
     * @return Server
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set host of server
     *
     * @param string $host
     * @return Server
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * set ports of server connections
     *
     * @param string $ports
     * @return Server
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
     * get current port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
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
        if (DEBUG) {
            echo Logger::add($data, Logger::INFO);
        }
        return $this->connection->sendData($data . IRC_EOL);
    }

    /**
     * Connect to server
     * @return bool
     * @throws Exception
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
                $this->port = current($ports);
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
    public function update()
    {
        $data = $this->connection->getData() ?? '';
        if (!$data) {
            return false;
        }

        $message = trim(str_replace([chr(9), chr(10), chr(11), chr(13), chr(0)], '', $data));

        if (!$message) {
            return false;
        }
        if (DEBUG) {
            echo Logger::add($message, Logger::INFO);
        }
        try {
            $this->textline->update($message);
        } catch (RuntimeException $error) {
            Logger::add($error->getMessage(), Logger::ERROR);
            return false;
        }
        return true;
    }
}