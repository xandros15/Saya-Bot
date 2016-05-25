<?php

namespace Ctcp;

use Saya\Core\Bot;
use Saya\Configuration\Configuration;
use Saya\IRC;
use Saya\Client\Module;

class Ctcp extends Module
{
    const CTCP_BRACKET = "\x01";
    const VERSION = 'VERSION';

    public function execute()
    {
        $this->on(IRC::PRIVMSG, 'ctcp');
    }

    protected function ctcp()
    {
        if (!preg_match("/^\x01(.*)\x01$/", $this->bot->getMessage(), $matches)) {
            return;
        }
        switch (strtoupper($matches[1])) {
            case self::VERSION:
                $this->sendVersion();
                return;
            default:
                return;
        }
    }

    protected function sendVersion()
    {
        $this->reply($this->getParsedMessage(self::VERSION, Configuration::VERSION), IRC::NOTICE);
    }

    protected function getParsedMessage($command, $message)
    {
        return
            self::CTCP_BRACKET .
            strtr('{command} {message}',
                [
                    '{command}' => $command,
                    '{message}' => $message,
                ]) .
            self::CTCP_BRACKET;
    }
}