<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-28
 * Time: 01:23
 */

namespace Saya\Core\IRC;


use Saya\Core\IRC\Response\Away;
use Saya\Core\IRC\Response\Channel;
use Saya\Core\IRC\Response\ChannelLists;
use Saya\Core\IRC\Response\Gline;
use Saya\Core\IRC\Response\Initial;
use Saya\Core\IRC\Response\Invitational;
use Saya\Core\IRC\Response\Lists;
use Saya\Core\IRC\Response\Luser;
use Saya\Core\IRC\Response\Server;
use Saya\Core\IRC\Response\Silence;
use Saya\Core\IRC\Response\Stats;
use Saya\Core\IRC\Response\Tracing;
use Saya\Core\IRC\Response\User;
use Saya\Core\IRC\Response\Whois;

interface Response extends Away, Channel, ChannelLists, Gline, Initial, Invitational, Lists, Luser, Server, Silence, Stats, Tracing, User, Whois
{
    const
        RPL_NONE = 0;
}