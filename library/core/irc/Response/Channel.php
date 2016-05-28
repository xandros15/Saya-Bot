<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:16
 */

namespace Saya\Core\IRC\Response;


interface Channel
{
    /* Post-Channel Join */
    const
        RPL_UNIQ_OP_IS = 325,
        RPL_CHANNEL_MODE_IS = 324, // <channel> <mode>
        RPL_CHANNEL_URL = 328, // <channel> :url                                                                   DALNET
        RPL_CHANNEL_CREATED = 329, // <channel> <time>
        RPL_NO_TOPIC = 331, // <channel> :No topic is set.
        RPL_TOPIC = 332, // <channel> :<topic>
        RPL_TOPIC_SET_BY = 333, // <channel> <nickname> <time>
        RPL_NAME_REPLY = 353, // = <channel> :<names>
        RPL_END_OF_NAMES = 366; // <channel> :End of /NAMES list.
}