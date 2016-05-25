<?php

namespace Basic;

use Saya\Core\IRC;
use Saya\Core\Configuration\Configuration as Config;
use Saya\Core\Client\Module;

class Chatlog extends Module
{

    public function execute()
    {
        $this->saveData();
    }

    public function saveData($out = false)
    {
        $timestamp = '[' . date('H:i') . ']';
        $source = $this->bot->getSource();
        $message = $this->bot->getMessage();
        $userNick = $this->bot->getUserNick();
        $folder = implode(DIRECTORY_SEPARATOR, [Config::$logFolder, Config::getServerName()]);
        /* $userName = $this->getUserName(); */
        /* $userHost = $this->getUserHost(); */
        if (!$out) {
            switch ($this->bot->getType()) {
                case IRC::PRIVMSG:
                    $text = "{$timestamp} <{$userNick}> {$message}" . IRC_EOL;
                    if (strpos($source, '#') === 0) {
                        $filename = $source . '_' . date('Y-m-d');
                        $filepatch = implode(DIRECTORY_SEPARATOR, [$folder, substr($source, 1)]);
                    } else {
                        $filename = $source;
                        $filepatch = $folder;
                    }
                    static::push($text, $filepatch, $filename . '.log');
                    return $text;
                case IRC::JOIN:
                    break;
                /*   $text = "{$timestamp} {$userNick} (~{$userName}@{$userHost}) has joined.\r\n";
                  $filename = $message;
                  return \Library\IRC\SaveData::push($text, $filepatch, $filename . '.log'); */
                case IRC::PART:
                    break;
                /*   $info = ($this->message) ? ' (' . $this->message . ')' : '';
                  $text = $timestamp . ' ' . $this->userNick . ' (~' . $this->userName . '@' . $this->userHost . ') has parted.' . $info . "\r\n";
                  $filename = $this->source;
                  return \Library\IRC\SaveData::push($text, $filepatch, $filename . '.log'); */
                case IRC::QUIT:
                    break;
                /*  $info = ($this->message) ? ' (' . $this->message . ')' : '';
                  $text = $timestamp . ' ' . $this->userNick . ' (~' . $this->userName . '@' . $this->userHost . ') has quit.' . $info . "\r\n";
                  $filename = 'QUIT'; //$this->msgChan;
                  return \Library\IRC\SaveData::push($text, $filepatch, $filename . '.log'); */
                case IRC::KICK:
                    break;
                /*  $info = ($this->message) ? ' (' . $this->message . ')' : '';
                  $special = explode(' ', $this->source);
                  $text = $timestamp . ' * ' . $special [1] . ' was kicked by ' . $special[0] . ' ' . $info . "\r\n";
                  return \Library\IRC\SaveData::push($text, $filepatch, $filename . '.log'); */

                case IRC::NOTICE:
                    break;
                /* return \Library\IRC\SaveData::push($text, $filepatch, $filename . '.log'); */
            }
        } else {
            
        }
    }

    public static function push($msg, $pathname, $filename)
    {
        $pathname = strtolower($pathname);
        $filename = strtolower($filename);
        if (!is_dir($pathname)) {
            mkdir($pathname, 0777, true);
        }
        $dir = implode(DIRECTORY_SEPARATOR, [$pathname, $filename]);
        return file_put_contents($dir, $msg, FILE_APPEND);
    }
}
