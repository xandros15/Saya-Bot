<?php

namespace Library;

use Library\Server;

class User
{
    /** @var string */
    public $name;

    /** @var string */
    public $nickname;

    /** @var string */
    public $mask;

    /** @var Server */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * sending message to target
     * syntax: PRIVMSG <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param type $message
     * @return int
     */
    public function say($nameOrChan, $message)
    {
        $data = sprintf('PRIVMSG %s :%s', $nameOrChan, $message);

        return $this->server->sendData($data);
    }

    /**
     * sending notice to target
     * syntax: NOTICE <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param type $message
     * @return int
     */
    public function notice($nameOrChan, $message)
    {
        $data = sprintf('NOTICE %s :%s', $nameOrChan, $message);

        return $this->server->sendData($data);
    }

    /**
     * change nickname
     * syntax: NICK <nickname>
     *
     * @param type $nickname
     * @return int
     */
    public function nick($nickname)
    {
        $data = sprintf('NICK %s', $nickname);

        return $this->server->sendData($data);
    }

    /**
     * join to channel
     * syntax: JOIN <channels> [<keys>]
     *
     * @param type $channel
     * @return int
     */
    public function join($channel)
    {
        if (is_array($channel)) {
            $channel = explode(',', $channel);
        }

        $data = sprintf('JOIN %s', $channel);

        return $this->server->sendData($data);
    }

    /**
     * part from channel
     * syntax: PART <channels> [<message>]
     *
     * @param type $channel
     * @param type $message
     * @return int
     */
    public function part($channel, $message = '')
    {
        if (is_array($channel)) {
            $channel = explode(',', $channel);
        }

        $data = sprintf('PART %s :%s', $channel, $message);

        return $this->server->sendData($data);
    }

    /**
     * quit from server
     * syntax: QUIT [<message>]
     *
     * @param type $message
     * @return type
     */
    public function quit($message = '')
    {
        $data = sprintf('QUIT :%s', $message);

        return $this->server->sendData($data);
    }

    /**
     * kick from channel
     * syntax: KICK <channel> <client> [<message>]
     *
     * @param type $name
     * @param type $message
     * @return type
     */
    public function kick($channel, $name, $message = '')
    {
        $data = sprintf('KICK %s %s :%s', $channel, $name, $message);

        return $this->server->sendData($data);
    }

    /**
     * set mode
     * syntax:
     *  <nickname> <flags> (user)
     *  <channel> <flags> [<args>]
     *
     * @param string $nameOrChan
     * @param string $flags
     * @param array $args
     * @return int
     */
    public function mode($nameOrChan, $flags, array $args)
    {
        if (is_array($args)) {
            $args = explode(' ', $args);
        }

        $data = spintf('MODE %s %s %s', $nameOrChan, $flags, $args);

        return $this->server->sendData($data);
    }

    /**
     * set topic of channel
     * syntax: TOPIC <channel> [<topic>]
     *
     * @param type $channel
     * @param type $topic
     * @return int
     */
    public function topic($channel, $topic)
    {

        $data = sprintf('TOPIC %s %s', $channel, $topic);

        return $this->server->sendData($data);
    }

    /**
     * Invite to channel
     * syntax: INVITE <nickname> <channel>
     *
     * @param type $nickname
     * @param type $channel
     * return int
     */
    public function invite($nickname, $channel)
    {
        $data = sprintf('INVITE %s %s', $nickname, $channel);

        return $this->server->sendData($data);
    }

    /**
     * set bot away on server
     * syntax: AWAY [<message>]
     *
     * @param type $message
     * @return type
     */
    public function away($message)
    {
        $data = sprintf('AWAY %s', $message);

        return $this->server->sendData($data);
    }

    /**
     * ping to server
     * syntax: PING <server1> [<server2>]
     * 
     * @param type $message
     * @return type
     */
    public function ping($message = '')
    {
        $message ? : sprintf('%s:%s', $this->server->getHost(), $this->server->getPort());

        $data = sprintf('PING %s', $message);

        return $this->server->sendData($data);
    }
}