<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:18
 */

namespace Saya\Core\IRC\Response;


interface Server
{
    /* server/misc */
    const
        RPL_VERSION = 351, // <version>.<debug> <server> :<info>
        RPL_INFO = 371, // :<info>
        RPL_END_OF_INFO = 374, // :End of /INFO list.
        RPL_YOURE_OPER = 381, // :You are now an IRC Operator
        RPL_REHASHING = 382, // <file> :Rehashing
        RPL_YOURE_SERVICE = 383,
        RPL_TIME = 391, // <server> :<time>
        RPL_USERS_START = 392,
        RPL_USERS = 393,
        RPL_END_OF_USERS = 394,
        RPL_NO_USERS = 395,
        RPL_SERV_LIST = 234,
        RPL_SERV_LIST_END = 235,
        RPL_ADMIN_ME = 256, // :Administrative info about server
        RPL_ADMIN_LOC1 = 257, // :<info>
        RPL_ADMIN_LOC2 = 258, // :<info>
        RPL_ADMIN_EMAIL = 259, // :<info>
        RPL_TRY_AGAIN = 263; // :Server load is temporarily too heavy. Please wait a while and try again.
}