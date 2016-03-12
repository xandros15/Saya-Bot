<?php

namespace Library\Chatter;

class MessageRelay
{
    private $message;

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
