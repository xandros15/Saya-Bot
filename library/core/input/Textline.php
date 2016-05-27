<?php

namespace Saya\Core\Input;

class Textline implements Input
{
    /**
     * @var string
     */
    protected $input;

    /**
     * @var MessageInterface
     */
    protected $message;

    /**
     * Textline constructor.
     */
    public function __construct()
    {
        $this->message = new Message($this);
    }

    /**
     * @return string
     */
    public function getInput() : string
    {
        return $this->input;
    }

    /**
     * @return MessageInterface
     */
    public function getMessage() : MessageInterface
    {
        return $this->message;
    }

    /**
     * @param string $input
     */
    public function update(string $input)
    {
        $this->input = $input;
        $this->message->update();
    }
}
