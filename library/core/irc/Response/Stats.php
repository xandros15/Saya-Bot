<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 02:18
 */

namespace Saya\Core\IRC\Response;


interface Stats
{
    /* stats */
    const
        RPL_STATS_LINK_INFO = 211, // <connection> <sendq> <sentmsg> <sentbyte> <recdmsg> <recdbyte> :<open>
        RPL_STATS_COMMANDS = 212, // <command> <uses> <bytes>
        RPL_STATS_CLINE = 213, // C <address> * <server> <port> <class>
        RPL_STATS_NLINE = 214, // N <address> * <server> <port> <class>
        RPL_STATS_ILINE = 215, // I <ipmask> * <hostmask> <port> <class>
        RPL_STATS_KLINE = 216, // k <address> * <username> <details>
        RPL_STATS_PLINE = 217, // P <port> <??!> <??!>
        RPL_STATS_QLINE = 222, // <mask> :<comment>
        RPL_STATS_ELINE = 223, // E <hostmask> * <username> <??!> <??!>
        RPL_STATS_DLINE = 224, // D <ipmask> * <username> <??!> <??!>
        RPL_STATS_LLINE = 241, // L <address> * <server> <??!> <??!>
        RPL_STATS_UPTIME = 242, // :Server Up <num> days, <time>
        RPL_STATS_OLINE = 243, // o <mask> <password> <user> <??!> <class>
        RPL_STATS_HLINE = 244, // H <address> * <server> <??!> <??!>
        RPL_STATS_GLINE = 247, // G <address> <timestamp> :<reason>
        RPL_STATS_ULINE = 248, // U <host> * <??!> <??!> <??!>
        RPL_STATS_ZLINE = 249, // :info
        RPL_STATS_YLINE = 218, // Y <class> <ping> <freq> <maxconnect> <sendq>
        RPL_END_OF_STATS = 219; // <char> :End of /STATS report
}