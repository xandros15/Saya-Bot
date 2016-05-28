<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:16
 */

namespace Saya\Core\IRC\Response;


interface ChannelLists
{
    /*  Channel Lists */
    const
        RPL_INVITE_LIST = 346, // <channel> <invite> <nick> <time>                                                 IRCNET
        RPL_END_OF_INVITE_LIST = 357, // <channel> :End of Channel Invite List                                            IRCNET
        RPL_EXCEPT_LIST = 348, // <channel> <exception> <nick> <time>                                              IRCNET
        RPL_END_OF_EXCEPT_LIST = 349, // <channel> :End of Channel Exception List                                         IRCNET
        RPL_BAN_LIST = 367, // <channel> <ban> <nick> <time>
        RPL_END_OF_BAN_LIST = 368; // <channel> :End of Channel Ban List
}