<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-31
 * Time: 15:46
 */

namespace Saya\Core\Output\Buffer;


use Saya\Core\Output\Sender;
use SplQueue;

class Buffer extends SplQueue implements TimeBuffer, Sender
{
    protected $delayTime;
    protected $sender;
    private $timeLastSend = 0;

    /**
     * Buffer constructor.
     * @param float $delayTime
     * @param Sender $sender
     */
    public function __construct(float $delayTime, Sender $sender)
    {
        $this->delayTime = $delayTime;
        $this->sender = $sender;
    }

    /**
     * @return bool
     */
    public function canSend() : bool
    {
        return $this->delayTime < (microtime(true) - $this->timeLastSend);
    }

    /**
     * @param float $time
     */
    public function changeDelayTime(float $time)
    {
        $this->delayTime = $time;
    }

    public function flushBuffer()
    {
        while (!$this->isEmpty() && $this->canSend()) {
            $this->sender->send($this->shift());
            $this->timeLastSend = microtime(true);
        }
    }

    /**
     * @param string $message
     */
    public function send(string $message)
    {
        $this->push($message);
    }
}