<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-29
 * Time: 15:56
 */

namespace Saya\Core\Output;


use Saya\Core\Input\MessageInterface;

class Reply extends Request
{
    /**
     * same as notice, just reply message
     *
     * @param $message
     * @param MessageInterface $input
     */
    public function replyNotice($message, MessageInterface $input)
    {
        $this->notice($input->getSource(), $message);
    }

    /**
     * same as say, just reply message
     *
     * @param $message
     * @param MessageInterface $input
     */
    public function reply($message, MessageInterface $input)
    {
        $this->say($input->getSource(), $message);
    }
}