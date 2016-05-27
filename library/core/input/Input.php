<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-26
 * Time: 02:18
 */

namespace Saya\Core\Input;

interface Input
{
    public function getInput() : string;

    public function getMessage() : MessageInterface;
}