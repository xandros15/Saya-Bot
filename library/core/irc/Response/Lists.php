<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:15
 */

namespace Saya\Core\IRC\Response;


interface Lists
{
    /* List */
    const
        RPL_LIST_START = 321, // Channel :Users Name
        RPL_LIST = 322, // <channel> <users> :<topic>
        RPL_LIST_END = 323, // :End of /LIST
        RPL_LINKS = 364, // <server> <hub> :<hops> <info>
        RPL_END_OF_LINKS = 365; // <mask> :End of /LINKS list.
}