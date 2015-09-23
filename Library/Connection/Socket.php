<?php

namespace Library\Connection;

class Socket implements \Library\BotInterface\Connection
{
    private $socket;
    private $host;
    private $port;

    public function __destruct()
    {
        $this->disconnect();
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function connect($host, $port)
    {
        if (strpos($host, ':') !== false) {
            $host = '[' . $host . ']';
        }

        $dns = sprintf('tcp://%s:%d', $host, $port);

        $socket = @stream_socket_client($dns, $errno, $errstr, 1);
        //TODO error holder
        if ($socket === false) {
            return false;
        }

        stream_set_blocking($socket, false);

        $this->port = (int) $port;
        $this->host = (string) $host;
        $this->socket = $socket;
        return true;
    }

    public function disconnect()
    {
        if (!is_resource($this->socket)) {
            return false;
        }
        stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
        stream_set_blocking($this->socket, false);
        fclose($this->socket);
        return true;
    }

    public function isConnected()
    {
        $socket = $this->socket;
        return (!is_resource($socket)) ? false : (!feof($socket));
    }

    public function sendData($data)
    {
        return fwrite($this->socket, $data, strlen($data));
    }

    public function getData()
    {
        return fgets($this->socket, 512);
    }

    public function setTimeout($seconds)
    {
        stream_set_timeout($this->socket, $seconds);
        return $this;
    }
}
