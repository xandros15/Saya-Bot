<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:22
 */

namespace Saya\Core\IRC\Response;


interface Luser
{
    /* LUser */
    const
        RPL_LUSER_CLIENT = 251, // :There are <user> users and <invis> invisible on <serv> servers
        RPL_LUSER_OP = 252, // <num> :operator(s) online
        RPL_LUSER_UNKNOWN = 253, // <num> :unknown connection(s)
        RPL_LUSER_CHANNELS = 254, // <num> :channels formed
        RPL_LUSER_ME = 255, // :I have <user> clients and <serv> servers
        RPL_LUSER_LOCAL_USER = 265, // :Current local users: <curr> Max: <max>
        RPL_LUSER_GLOBAL_USER = 266; // :Current global users: <curr> Max: <max>
}