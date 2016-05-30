<?php

namespace Saya\Core\Output;

use Saya\Core\Server\ServerInfo;
use Saya\Core\IRC;

class Request implements RequestInterface
{
    /** @var Sender */
    protected $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * sending message to target
     * syntax: PRIVMSG <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     */
    public function say(string $nameOrChan, string $message)
    {
        $data = sprintf('PRIVMSG %s :%s', $nameOrChan, $message);

        $this->sender->send($data);
    }

    /**
     * sending notice to target
     * syntax: NOTICE <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     */
    public function notice(string $nameOrChan, string $message)
    {
        $data = sprintf('NOTICE %s :%s', $nameOrChan, $message);

        $this->sender->send($data);
    }

    /**
     * change nickname
     * syntax: NICK <nickname>
     *
     * @param string $nickname
     */
    public function nick(string $nickname)
    {
        $data = sprintf('NICK %s', $nickname);

        $this->sender->send($data);
    }

    /**
     * join to channel
     * syntax: JOIN <channels> [<keys>]
     *
     * @param array $channel
     */
    public function join(array $channel)
    {

        $data = sprintf('JOIN %s', explode(',', $channel));

        $this->sender->send($data);
    }

    /**
     * part from channel
     * syntax: PART <channels> [<message>]
     *
     * @param array $channel
     * @param string $message
     */
    public function part(array $channel, string $message = '')
    {
        $data = sprintf('PART %s :%s', explode(',', $channel), $message);

        return $this->sender->send($data);
    }

    /**
     * quit from server
     * syntax: QUIT [<message>]
     *
     * @param string $message
     */
    public function quit(string $message = '')
    {
        $data = sprintf('QUIT :%s', $message);

        $this->sender->send($data);
    }

    /**
     * kick from channel
     * syntax: KICK <channel> <client> [<message>]
     *
     * @param string $channel
     * @param string $name
     * @param string $message
     */
    public function kick(string $channel, string $name, string $message = '')
    {
        $data = sprintf('KICK %s %s :%s', $channel, $name, $message);

        $this->sender->send($data);
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
     */
    public function mode(string $nameOrChan, string $flags, array $args)
    {
        $data = sprintf('MODE %s %s %s', $nameOrChan, $flags, implode(' ', $args));

        $this->sender->send($data);
    }

    /**
     * set topic of channel
     * syntax: TOPIC <channel> [<topic>]
     *
     * @param string $channel
     * @param string $topic
     */
    public function topic(string $channel, string $topic)
    {

        $data = sprintf('TOPIC %s %s', $channel, $topic);

        $this->sender->send($data);
    }

    /**
     * Invite to channel
     * syntax: INVITE <nickname> <channel>
     *
     * @param string $nickname
     * @param string $channel
     */
    public function invite(string $nickname, string $channel)
    {
        $data = sprintf('INVITE %s %s', $nickname, $channel);

        $this->sender->send($data);
    }

    /**
     * set bot away on server
     * syntax: AWAY [<message>]
     *
     * @param string $message
     */
    public function away(string $message)
    {
        $data = sprintf('AWAY %s', $message);

        $this->sender->send($data);
    }

    /**
     * ping to server
     * syntax: PING <server1> [<server2>]
     *
     * @param ServerInfo $serverInfo
     * @param string $message
     */
    public function ping(ServerInfo $serverInfo, string $message = '')
    {
        $data = sprintf('PING %s', $message ?: sprintf('%s:%s', $serverInfo->getHost(), $serverInfo->getPort()));

        $this->sender->send($data);
    }
}