<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-26
 * Time: 17:42
 */

namespace Saya\Core\Output;


use Saya\Core\Server\ServerInfo;

interface RequestInterface
{
    /**
     * sending message to target
     * syntax: PRIVMSG <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     */
    public function say(string $nameOrChan, string $message);

    /**
     * sending notice to target
     * syntax: NOTICE <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     */
    public function notice(string $nameOrChan, string $message);

    /**
     * change nickname
     * syntax: NICK <nickname>
     *
     * @param string $nickname
     */
    public function nick(string $nickname);

    /**
     * join to channel
     * syntax: JOIN <channels> [<keys>]
     *
     * @param array $channel
     */
    public function join(array $channel);

    /**
     * part from channel
     * syntax: PART <channels> [<message>]
     *
     * @param array $channel
     * @param string $message
     */
    public function part(array $channel, string $message = '');

    /**
     * quit from server
     * syntax: QUIT [<message>]
     *
     * @param string $message
     */
    public function quit(string $message = '');

    /**
     * kick from channel
     * syntax: KICK <channel> <client> [<message>]
     *
     * @param $channel
     * @param $name
     * @param string $message
     */
    public function kick(string $channel, string $name, string $message = '');

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
    public function mode(string $nameOrChan, string $flags, array $args);

    /**
     * set topic of channel
     * syntax: TOPIC <channel> [<topic>]
     *
     * @param string $channel
     * @param string $topic
     */
    public function topic(string $channel, string $topic);

    /**
     * Invite to channel
     * syntax: INVITE <nickname> <channel>
     *
     * @param string $nickname
     * @param string $channel
     */
    public function invite(string $nickname, string $channel);

    /**
     * set bot away on server
     * syntax: AWAY [<message>]
     *
     * @param string $message
     */
    public function away(string $message);

    /**
     * ping to server
     * syntax: PING <server1> [<server2>]
     *
     * @param ServerInfo $serverInfo
     * @param string $message
     */
    public function ping(ServerInfo $serverInfo, string $message = '');
}