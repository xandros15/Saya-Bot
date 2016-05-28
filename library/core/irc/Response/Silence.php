<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:19
 */

namespace Saya\Core\IRC\Response;


interface Silence
{
    /* Silence */
    const
        RPL_SILENCE_LIST = 271, // <nick> <mask>                                                                    UNDERNET/DALNET
        RPL_END_OF_SILENCE_LIST = 272; // <nick> :End of Silence List                                                      UNDERNET/DALNET

}