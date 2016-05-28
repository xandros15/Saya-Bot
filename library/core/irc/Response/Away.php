<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:13
 */

namespace Saya\Core\IRC\Response;


interface Away
{
    /* Away */
    const
        RPL_AWAY = 301, // <nick> :away
        RPL_UNAWAY = 305, // :You are no longer marked as being away
        RPL_NOW_AWAY = 306; // :You have been marked as being away
}