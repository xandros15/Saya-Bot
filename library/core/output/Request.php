<?php

namespace Saya\Core\Output;

use Saya\Core\Server\ServerInfo;
use Saya\Core\Server\ServerInterface;
use Saya\Core\Input\Input;
use Saya\Core\Input\MessageInterface;
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
     * @return int
     */
    public function say($nameOrChan, $message)
    {
        $data = sprintf('PRIVMSG %s :%s', $nameOrChan, $message);

        return $this->sender->send($data);
    }

    /**
     * same as say, just reply message
     *
     * @param int $message
     * @param MessageInterface $input
     * @return int
     */
    public function reply($message, MessageInterface $input)
    {
        return $this->say($input->getSource(), $message);
    }

    /**
     * sending notice to target
     * syntax: NOTICE <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     * @return int
     */
    public function notice($nameOrChan, $message)
    {
        $data = sprintf('NOTICE %s :%s', $nameOrChan, $message);

        return $this->sender->send($data);
    }

    /**
     * same as notice, just reply message
     *
     * @param int $message
     * @return int
     */
    public function replyNotice($message, MessageInterface $input)
    {
        return $this->notice($input->getSource(), $message);
    }

    /**
     * change nickname
     * syntax: NICK <nickname>
     *
     * @param string $nickname
     * @return int
     */
    public function nick($nickname)
    {
        $data = sprintf('NICK %s', $nickname);

        return $this->sender->send($data);
    }

    /**
     * join to channel
     * syntax: JOIN <channels> [<keys>]
     *
     * @param string $channel
     * @return int
     */
    public function join($channel)
    {
        if (is_array($channel)) {
            $channel = explode(',', $channel);
        }

        $data = sprintf('JOIN %s', $channel);

        return $this->sender->send($data);
    }

    /**
     * part from channel
     * syntax: PART <channels> [<message>]
     *
     * @param string $channel
     * @param string $message
     * @return int
     */
    public function part($channel, $message = '')
    {
        if (is_array($channel)) {
            $channel = explode(',', $channel);
        }

        $data = sprintf('PART %s :%s', $channel, $message);

        return $this->sender->send($data);
    }

    /**
     * quit from server
     * syntax: QUIT [<message>]
     *
     * @param string $message
     * @return int
     */
    public function quit($message = '')
    {
        $data = sprintf('QUIT :%s', $message);

        return $this->sender->send($data);
    }

    /**
     * kick from channel
     * syntax: KICK <channel> <client> [<message>]
     *
     * @param string $name
     * @param string $message
     * @return int
     */
    public function kick($channel, $name, $message = '')
    {
        $data = sprintf('KICK %s %s :%s', $channel, $name, $message);

        return $this->sender->send($data);
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
        $data = sprintf('MODE %s %s %s', $nameOrChan, $flags, implode(' ', $args));

        return $this->sender->send($data);
    }

    /**
     * set topic of channel
     * syntax: TOPIC <channel> [<topic>]
     *
     * @param string $channel
     * @param string $topic
     * @return int
     */
    public function topic($channel, $topic)
    {

        $data = sprintf('TOPIC %s %s', $channel, $topic);

        return $this->sender->send($data);
    }

    /**
     * Invite to channel
     * syntax: INVITE <nickname> <channel>
     *
     * @param string $nickname
     * @param string $channel
     * @return int
     */
    public function invite($nickname, $channel)
    {
        $data = sprintf('INVITE %s %s', $nickname, $channel);

        return $this->sender->send($data);
    }

    /**
     * set bot away on server
     * syntax: AWAY [<message>]
     *
     * @param string $message
     * @return int
     */
    public function away($message)
    {
        $data = sprintf('AWAY %s', $message);

        return $this->sender->send($data);
    }

    /**
     * ping to server
     * syntax: PING <server1> [<server2>]
     *
     * @param string $message
     * @return int
     */
    public function ping($message = '', ServerInfo $serverInfo)
    {
        $message ?: sprintf('%s:%s', $serverInfo->getHost(), $serverInfo->getPort());

        $data = sprintf('PING %s', $message);

        return $this->sender->send($data);
    }
}