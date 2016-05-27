<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-26
 * Time: 17:54
 */

namespace Saya\Core\Server;


interface ServerInfo
{
    public function getPort();

    public function getHost();

    public function getName();
}