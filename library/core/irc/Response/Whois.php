<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:13
 */

namespace Saya\Core\IRC\Response;


interface Whois
{

    /* WHOIS/WHOWAS */
    const
        RPL_WHO_IS_HELPER = 310, // <nick> :looks very helpful                                                       DALNET
        RPL_WHO_IS_USER = 311, // <nick> <username> <address> * :<info>
        RPL_WHO_IS_SERVER = 312, // <nick> <server> :<info>
        RPL_WHO_IS_OPERATOR = 313, // <nick> :is an IRC Operator
        RPL_WHO_IS_IDLE = 317, // <nick> <seconds> <signon> :<info>
        RPL_END_OF_WHOIS = 318, // <request> :End of /WHOIS list.
        RPL_WHO_IS_CHANNELS = 319, // <nick> :<channels>
        RPL_WHO_WAS_USER = 314, // <nick> <username> <address> * :<info>
        RPL_END_OF_WHO_WAS = 369, // <request> :End of WHOWAS
        RPL_WHO_REPLY = 352, // <channel> <username> <address> <server> <nick> <flags> :<hops> <info>
        RPL_END_OF_WHO = 315, // <request> :End of /WHO list.
        RPL_USER_IPS = 307, // :userips                                                                         UNDERNET
        RPL_USER_IP = 340; // <nick> :<nickname>=+<user>@<IP.address>
}