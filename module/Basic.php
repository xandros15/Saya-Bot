<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Thpl
 *
 * @author ASUS
 */

namespace module;

use library\Module;
use library\constants\IRC;
use library\configuration as Config;

class Basic extends Module
{
    const
        NICKSERV = 'NickServ',
        CHANSERV = 'ChanServ';

    private
        $nickCounter = 0,
        $ghost,
        $nameReplyBuffer = [];

    public function execute()
    {
        $this->on(IRC::JOIN, 'addChannel');
        $this->on(IRC::KICK, 'reJoin');
        $this->on(IRC::RplWelcome, 'ghostInDaShell');
        $this->on(IRC::RplWelcome, 'joinOnLogin');
        $this->on(IRC::ErrNickNameInUse, 'nickNameInUse');
        $this->on(IRC::PING, 'pong');
        $this->on(IRC::ErrErroneusNickname, 'CloseBot', ['To Large Nickname']);
        $this->on(IRC::RplNamReply, 'addNickToChannel');
        $this->on(IRC::RplEndOfNames, 'addNickToChannel', [true]);
        $this->on(IRC::ErrBannedFromChan, 'unban');
        if(Config::$serverName == 'quakenet'){
            $this->on(IRC::RplWelcome, 'loginToQ');
        }


        if ($this->bot->getUserNick() == self::NICKSERV) {
            $this->on(IRC::NOTICE, 'nickServ');
        }
    }
    protected function loginToQ()
    {
        $this->message('auth tokido ' . Config::$personal->password, 'Q@CServe.quakenet.org');
    }

    protected function addChannel()
    {
        if (Config::getNick() == $this->bot->getUserNick()) {
            $channel = ($this->bot->getSource()) ? $this->bot->getSource() : $this->bot->getMessage();
            $this->bot->channelList[$channel] = [];
        }
    }

    protected function reJoin()
    {
        if (Config::getNick() == $this->bot->getOffset()) {
            $this->message($this->bot->getSource(), null, IRC::JOIN);
        }
    }

    protected function nickServ()
    {
        if (strpos($this->bot->getMessage(), 'This nickname is registered') !== false && !empty(Config::$personal->identify)) {
            $this->message(Config::$personal->identify, null, IRC::IDENTIFY);
        }

        if ($this->ghost == true && strpos($this->bot->getMessage(), 'Ghost with your nick has been killed') !== false) {
            $this->message(Config::$personal->nick, null, IRC::NICK, true);
        }
    }

    protected function unban()
    {
        $this->message('UNBAN ' . $this->bot->getSource(), self::CHANSERV, IRC::PRIVMSG, true);
        $this->message($this->bot->getSource(), null, IRC::JOIN);
    }

    protected function pong()
    {
        /* tell me where i need to send pong? */
        if (Config::getServerName() == 'quakenet') {
            $this->message($this->bot->getMessage(), null, IRC::PING, true);
        } else {
            $this->message(Config::$server . ':' . Config::$port, null, IRC::PING, true);
        }
    }

    protected function addNickToChannel($done = false)
    {
        $source = $this->bot->getSource();
        if ($done && !empty($this->bot->channelList[$source])) {
            array_push((array) $this->bot->channelList[$source], explode(' ', $this->nameReplyBuffer[$source]));
            unset($this->nameReplyBuffer[$source]);
        } else {
            if (empty($this->nameReplyBuffer[$source])) {
                $this->nameReplyBuffer[$source] = '';
            }
            if (isset($this->bot->channelList[$source])) {
                $this->nameReplyBuffer[$source] .= $this->bot->getMessage();
            }
        }
    }

    protected function nickNameInUse()
    {
        Config::setNick(Config::getNick() . ++$this->nickCounter);
        $this->message(Config::getNick(), null, IRC::NICK);
        $this->ghost = true;
    }

    protected function ghostInDaShell()
    {
        if ($this->ghost) {
            $message = 'GHOST' . Config::$personal->nick . ' ' . Config::$personal->identify;
            $this->message($message, self::NICKSERV, IRC::PRIVMSG);
            $this->ghost = false;
        }
    }

    protected function CloseBot($message)
    {
        die($message);
    }

    protected function joinOnLogin()
    {
        $channel = Config::$channels;
        if (empty($channel)) {
            return;
        } elseif (is_array($channel)) {
            $this->message(implode(',', $channel), null, IRC::JOIN);
        } else {
            $this->message($channel, null, IRC::JOIN);
        }
    }
}
