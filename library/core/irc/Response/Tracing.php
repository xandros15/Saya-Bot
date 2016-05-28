<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:18
 */

namespace Saya\Core\IRC\Response;


interface Tracing
{
    /* tracing */
    const
        RPL_TRACE_LINK = 200,
        RPL_TRACE_CONNECTING = 201,
        RPL_TRACE_HANDSHAKE = 202,
        RPL_TRACE_UNKNOWN = 203,
        RPL_TRACE_OPERATOR = 204,
        RPL_TRACE_USER = 205,
        RPL_TRACE_SERVER = 206,
        RPL_TRACE_SERVICE = 207,
        RPL_TRACE_NEW_TYPE = 208,
        RPL_TRACE_CLASS = 209,
        RPL_TRACE_RECONNECT = 210,
        RPL_TRACE_LOG = 261,
        RPL_TRACE_END = 262;
}