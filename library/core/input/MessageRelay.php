<?php

namespace Saya\Core\Input;

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
