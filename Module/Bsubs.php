<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module;

use Library\Constants\IRC;
use Library\Configuration as Config;

/**
 * Description of Bsubs
 *
 * @author ASUS
 */
class Bsubs extends \Library\Module
{
    private $userList = [];
    private $privcommands = [];

    public function execute()
    {
        $this->on(IRC::JOIN, 'addUser');
        $this->on(IRC::PART, 'delUser');
        $this->on(IRC::QUIT, 'delUser');
        $this->on(IRC::PRIVMSG, 'changeStatus');
        $this->on(IRC::PRIVMSG, 'sendLink');
        $this->on(IRC::RplNamReply, 'addUser');
        $this->on(IRC::NICK, 'addUser');
    }

    public function addUser()
    {
        switch ($this->bot->getType()) {
            case IRC::JOIN:
                return $this->userList[$this->bot->getUserNick()] = false;
            case IRC::RplNamReply:
                foreach (explode(' ', $this->bot->getMessage()) as $user) {
                    $this->userList[ltrim($user, '!@%&~+')] = 0;
                }
                return;
            case IRC::NICK:
                $nick = $this->bot->getUserNick();
                $holder = $this->userList[$nick];
                unset($this->userList[$nick]);
                $this->userList[$this->bot->getMessage()] = $holder;
                return;
        }
    }

    public function delUser()
    {
        unset($this->userList[$this->bot->getUserNick()]);
    }

    public function changeStatus()
    {
        $channels = $this->getJoinedChannel();
        if (!in_array($this->bot->getSource(), $channels)) {
            return;
        }
        $userNick = $this->bot->getUserNick();
        if (!isset($this->userList[$userNick]) || $this->userList[$userNick] !== false) {
            return;
        }
        $this->userList[$userNick] = time();
    }

    public function sendLink()
    {
        $message = $this->bot->getMessage();
        if (strpos($message, Config::$commandPrefix) !== 0) {
            return;
        }
        $trigger = substr($message, 1);
        if (!isset($this->privcommands[$trigger])) {
            return;
        }
        $command = $this->privcommands[$trigger];
        $channel = $this->bot->getSource();
        if (!in_array($channel, $command['channels'])) {
            return;
        }
        $user = $this->bot->getUserNick();
        if (!isset($this->userList[$user]) || (time() - $this->userList[$user]) < 1) {
            return $this->message('Wypadaloby sie przywitac.', $channel . ' ' . $user, IRC::KICK);
        }
        $this->message($command['notice'], $user, IRC::NOTICE);
    }

    public function loadSettings($object = null)
    {
        $this->setPrivCommand([
            'trigger' => 'nonnon12',
            'notice' => 'prosze: https://mega.nz/#!m98nFJia!hO4TpY9Dtgi-s_NW7nmiUdNMyeXVe_VRsfJnRiQY0dU',
            'channels' => ['#b-subs']
        ]);

        $this->setPrivCommand([
            'trigger' => 'nonnon11',
            'channels' => ['#b-subs'],
            'notice' => 'prosze: https://mega.nz/#!qolFGZpb!T3Ic5DDJKOy4TBp1xHnDtT4T9ctzianr-ofW_EZ9eT0'
        ]);

        $this->setPrivCommand([
            'trigger' => 'okusama10',
            'notice' => 'prosze: https://mega.nz/#!vgVSXRiT!lMNLS_7CFKQWb79W_xOk6hruM3XDif_U3nIpBwzR4Pk',
            'channels' => ['#bodzio']
        ]);
        
        $this->setPrivCommand([
            'trigger' => 'pluton03',
            'notice' => 'prosze: https://mega.nz/#!X1MniS7a!2zHv4l6CafKeRZb2H5lh9FGhikAIyQ2khPccnaWLqCg',
            'channels' => ['#b-subs']
        ]);
        $this->setPrivCommand([
            'trigger' => 'pluton01',
            'notice' => 'prosze: https://mega.nz/#!rhsC3T4a!ymPFxn9vkaa3acZLSHOCabOqzGI5QNcUx906_6FgU8A',
            'channels' => ['#b-subs']
        ]);
        $this->setPrivCommand([
            'trigger' => 'pluton02',
            'notice' => 'prosze: https://mega.nz/#!70EHxT6L!72vKagsGVrxkN8m2MI8ySB_gQU0VUXS2u9_F5B073fs',
            'channels' => ['#b-subs']
        ]);

        parent::loadSettings($this);
    }

    public function setPrivCommand(array $command)
    {
        $this->privcommands[$command['trigger']] = $command;
    }
}
