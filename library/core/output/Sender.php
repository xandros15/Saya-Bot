<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-29
 * Time: 15:20
 */

namespace Saya\Core\Output;


interface Sender
{
    public function send(string $message);
}