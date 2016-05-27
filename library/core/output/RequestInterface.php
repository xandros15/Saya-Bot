<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-26
 * Time: 17:42
 */

namespace Saya\Core\Output;


interface RequestInterface
{
    /**
     * sending message to target
     * syntax: PRIVMSG <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     * @return int
     */
    public function say($nameOrChan, $message);

    /**
     * same as say, just reply message
     *
     * @param int $message
     * @return int
     */
    public function reply($message);

    /**
     * sending notice to target
     * syntax: NOTICE <msgtarget> <message>
     *
     * @param string $nameOrChan
     * @param string $message
     * @return int
     */
    public function notice($nameOrChan, $message);

    /**
     * same as notice, just reply message
     *
     * @param int $message
     * @return int
     */
    public function replyNotice($message);

    /**
     * change nickname
     * syntax: NICK <nickname>
     *
     * @param string $nickname
     * @return int
     */
    public function nick($nickname);

    /**
     * join to channel
     * syntax: JOIN <channels> [<keys>]
     *
     * @param string $channel
     * @return int
     */
    public function join($channel);

    /**
     * part from channel
     * syntax: PART <channels> [<message>]
     *
     * @param string $channel
     * @param string $message
     * @return int
     */
    public function part($channel, $message = '');

    /**
     * quit from server
     * syntax: QUIT [<message>]
     *
     * @param string $message
     * @return int
     */
    public function quit($message = '');

    /**
     * kick from channel
     * syntax: KICK <channel> <client> [<message>]
     *
     * @param string $name
     * @param string $message
     * @return int
     */
    public function kick($channel, $name, $message = '');

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
    public function mode($nameOrChan, $flags, array $args);
    /**
     * set topic of channel
     * syntax: TOPIC <channel> [<topic>]
     *
     * @param string $channel
     * @param string $topic
     * @return int
     */
    public function topic($channel, $topic);
    /**
     * Invite to channel
     * syntax: INVITE <nickname> <channel>
     *
     * @param string $nickname
     * @param string $channel
     * @return int
     */
    public function invite($nickname, $channel);

    /**
     * set bot away on server
     * syntax: AWAY [<message>]
     *
     * @param string $message
     * @return int
     */
    public function away($message);

    /**
     * ping to server
     * syntax: PING <server1> [<server2>]
     *
     * @param string $message
     * @return int
     */
    public function ping($message = '');
}