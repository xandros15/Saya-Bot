<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-31
 * Time: 18:03
 */

namespace Saya\Core\Output\Buffer;


interface TimeBuffer
{
    /**
     * @return bool
     */
    public function canSend() : bool;

    /**
     * @param float $time
     */
    public function changeDelayTime(float $time);

    public function flushBuffer();
}