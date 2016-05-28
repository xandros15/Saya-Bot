<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:19
 */

namespace Saya\Core\IRC\Response;


interface Gline
{
    /* GLINE */
    const
        RPL_GLINE_LIST = 280, // <address> <timestamp> <reason>                                                   UNDERNET
        RPL_END_OF_GLINE_LIST = 281; // :End of G-line List                                                              UNDERNET

}