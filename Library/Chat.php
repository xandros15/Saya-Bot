<?php

namespace Library;

use Library\Configuration as Config;

class Chat
{
    const
        MESSAGE_REGEX = '~^(?:[:](\S+) )?(\S+)(?: (?!:)(.+?))?(?: [:](.+))?$~',
        MASK_REGEX = '/^(?:(\S+)!~?(\S+)\@)?(\S+)$/',
        SOURCE_REGEX = '/(#\w+)/';

    public
        $mask,
        $message,
        $offset,
        $source,
        $type,
        $userName,
        $userNick,
        $userHost;

    public function setIncommingData($dataString)
    {
        if (!preg_match(self::MESSAGE_REGEX, $dataString, $data)) {
            return false;
        }
        $this->mask = (!empty($data[1])) ? $data[1] : false;
        if (!empty($this->mask)) {
            preg_match(self::MASK_REGEX, $this->mask, $mask);
            $this->userNick = (!empty($mask[1])) ? $mask[1] : false;
            $this->userName = (!empty($mask[2])) ? $mask[2] : false;
            $this->userHost = (!empty($mask[3])) ? $mask[3] : false;
        } else {
            $this->userNick = $this->userName = $this->userHost = false;
        }
        $this->type = (!empty($data[2])) ? strtoupper($data[2]) : false;
        $this->message = (!empty($data[4])) ? $data[4] : false;
        if (empty($data[3])) {
            $this->offset = $this->source = false;
        } elseif ($data[3] == Config::getNick()) {
            $this->source = $this->userNick;
            $this->offset = false;
        } elseif ($data[3]) {
            preg_match(self::SOURCE_REGEX, $data[3], $channel);
            $this->source = (!empty($channel[1])) ? strtolower($channel[1]) : '';
            $this->offset = trim(str_ireplace($this->source, '', $data[3]));
        }
        return true;
    }
}
