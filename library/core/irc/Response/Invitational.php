<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:17
 */

namespace Saya\Core\IRC\Response;


interface Invitational
{
    /* Invitational */
    const
        RPL_INVITING = 341, // <nick> <channel>
        RPL_SUMMONING = 342;
}