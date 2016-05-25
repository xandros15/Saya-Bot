<?php

namespace Saya\Core\Input;

use Saya\Core\Configuration\Configuration as Config;

class Textline
{
    const MESSAGE_REGEX = '~^(?:[:](\S+) )?(\S+)(?: (?!:)(.+?))?(?: [:](.+))?$~';
    const MASK_REGEX = '/^(?:(\S+)!~?(\S+)\@)?(\S+)$/';
    const SOURCE_REGEX = '/(#[\w-]+)/';

    private $user;
    private $message;
    private $offset;
    private $source;
    private $type;
    private $userName;
    private $userNick;
    private $userHost;
    private $messageRelay;

    public function __construct(MessageRelay $messageRelay)
    {
        $this->messageRelay = $messageRelay;
    }

    public function update()
    {
        $message = $this->messageRelay->getMessage();
        if (!preg_match(self::MESSAGE_REGEX, $message, $data)) {
            return false;
        }

        $this->setUser(isset($data[1]) ? $data[1] : null);
        $this->setType(isset($data[2]) ? $data[2] : null);
        $this->setSource(isset($data[3]) ? $data[3] : null);
        $this->setMessage(isset($data[4]) ? $data[4] : null);

        return true;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getUserNick()
    {
        return $this->userNick;
    }

    public function getUserHost()
    {
        return $this->userHost;
    }

    private function setUser($data)
    {
        $user = (!empty($data)) ? $data : false;
        if ($user && preg_match(self::MASK_REGEX, $user, $maskMatch)) {
            $this->userNick = (!empty($maskMatch[1])) ? $maskMatch[1] : false;
            $this->userName = (!empty($maskMatch[2])) ? $maskMatch[2] : false;
            $this->userHost = (!empty($maskMatch[3])) ? $maskMatch[3] : false;
        } else {
            $this->userNick = $this->userName = $this->userHost = false;
        }
        $this->user = $user;
    }

    private function setType($data)
    {
        $this->type = (!empty($data)) ? mb_strtoupper($data) : false;
    }

    private function setMessage($data)
    {
        $this->message = (!empty($data)) ? $data : false;
    }

    private function setSource($data)
    {
        if (empty($data)) {
            $offset = $source = false;
        } elseif ($data == Config::getNick()) {
            $source = $this->userNick;
            $offset = false;
        } else {
            preg_match(self::SOURCE_REGEX, $data, $channel);
            $source = (!empty($channel[1])) ? strtolower($channel[1]) : '';
            $offset = trim(str_ireplace($source, '', $data));
        }
        $this->offset = $offset;
        $this->source = $source;
    }
}
