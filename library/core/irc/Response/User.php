<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:23
 */

namespace Saya\Core\IRC\Response;


interface User
{
    /* IsOn/UserHost */
    const
        RPL_USER_HOST = 302, // :userhosts
        RPL_IS_ON = 303; // :nicknames
}