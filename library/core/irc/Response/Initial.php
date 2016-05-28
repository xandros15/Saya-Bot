<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:11
 */

namespace Saya\Core\IRC\Response;


interface Initial
{
    /* Initial */
    const
        RPL_WELCOME = 001, // :Welcome to the Internet Relay Network <nickname>
        RPL_YOUR_HOST = 002, // :Your host is <server>, running version <ver>
        RPL_CREATED = 003, // :This server was created <datetime>
        RPL_MY_INFO = 004, // <server> <ver> <usermode> <chanmode>
        RPL_MAP = 005, // :map
        RPL_END_OF_MAP = 007, // :End of /MAP
        RPL_MOTD_START = 375, // :- server Message of the Day
        RPL_MOTD = 372, // :- <info>
        RPL_MOTD_ALT = 377, // :- <info>                                                                        (some)
        RPL_MOTD_ALT_2 = 378, // :- <info>                                                                        (some)
        RPL_MOTD_END = 376, // :End of /MOTD command.
        RPL_U_MODE_IS = 221; // <mode>
}