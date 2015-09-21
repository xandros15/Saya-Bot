<?php

namespace Library\Connection;

use Exception;

class Socket implements \Library\BotInterface\Connection
{
    const
        NUMBER_OF_RECONNECTS = 10;
    
    private
        $server = '',
        $port = 0,
        $socket = null;

    public function __destruct()
    {
        $this->disconnect();
    }

    public function connect()
    {
        $port = $this->port;
        $server = $this->server;
        $try = self::NUMBER_OF_RECONNECTS;
        do {
            $this->socket = stream_socket_client($server . ':' . $port++);
        } while ($try-- > 0 && !$this->isConnected() && !sleep(1));
        if (!$this->isConnected()) {
            throw new Exception("Unable to connect to server via fsockopen with server: \"{$this->server}\" and port: \"{$this->port}\"");
        }
        stream_set_blocking($this->socket, 0);
        stream_set_timeout($this->socket, 360);
    }

    public function disconnect()
    {
        return ($this->socket) ? fclose($this->socket) : false;
    }

    public function sendData($data)
    {
        return fwrite($this->socket, $data, 510);
    }

    public function getData()
    {
        return fgets($this->socket, 512);
    }

    public function isConnected()
    {
        return (is_resource($this->socket)) ? true : false;
    }

    public function setServer($server)
    {
        $this->server = (string) $server;
    }

    public function setPort($port)
    {
        $this->port = (int) $port;
    }
}
