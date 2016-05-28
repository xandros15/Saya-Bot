<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 03:26
 */

namespace Saya\Core\IRC;


interface Command
{
    const
        PRIVMSG = 'PRIVMSG',
        MODE = 'MODE',
        TOPIC = 'TOPIC',
        PART = 'PART',
        QUIT = 'QUIT',
        JOIN = 'JOIN',
        KICK = 'KICK',
        NOTICE = 'NOTICE',
        INVITE = 'INVITE',
        NICK = 'NICK',
        IDENTIFY = 'IDENTIFY',
        PING = 'PING',
        PONG = 'PONG',
        AWAY = 'AWAY',
        USER = 'USER',
        PASSWORD = 'PASS';
}