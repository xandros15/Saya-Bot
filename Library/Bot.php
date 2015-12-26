<?php

namespace Library;

use Library\Server;
use Library\Configuration as Config;
use Library\Filter;
use Library\Constants\IRC;
use ReflectionClass;
use Exception;
use Library\Debugger\Logger;

/* Interfaces */

class Bot
{
    public
        $channelList = [],
        /** @var \Library\Module */
        $module = [];
    private
        $buffer = [],
        /** @var \Library\Chatter\Textline */
        $chat = null,
        $messageToSend = 0,
        $timeLastSend = 0,
        /** @var \Library\Server */
        $server,
        $numberOfReconnects = 0;

    public function connectToServer()
    {
        if (!$this->server->isConnected()) {
            if (!$this->server->connect()) {
                return false;
            }
        }
        $user = IRC::USER . ' ' . Config::$personal->name . ' exsubs.anidb.pl ' . Config::getNick() . ' :' . Config::$personal->name;
        $login = IRC::NICK . ' ' . Config::getNick();
        $auth = (Config::$personal->password) ? IRC::PASSWORD . ' ' . Config::$personal->password : false;
        if ($auth) {
            $this->sendDataToServer($auth);
        }
        $this->sendDataToServer($login);
        $this->sendDataToServer($user);
        return true;
    }

    public function fillBuffer($text, $type, $target = null, $prio = false)
    {
        $text = str_replace([chr(9), chr(10), chr(11), chr(13), chr(0)], '', $text);
        $text = trim($text);
        $filters = (new Filter())->filterList();
        if ($type == IRC::PRIVMSG && $filters) {
            foreach ($filters as $filter) {
                if (isset($filter['serverBlock']) && in_array(Config::getServerName(), $filter['serverBlock'])) {
                    continue;
                }
                if (isset($filter['serverAllow']) && !in_array(Config::getServerName(), $filter['serverAllow'])) {
                    continue;
                }
                if (isset($filter['channelBlock']) && in_array($target, $filter['channelBlock'])) {
                    continue;
                }
                if (isset($filter['channelAllow']) && !in_array($target, $filter['channelAllow'])) {
                    continue;
                }
                if (is_callable($filter['callback'])) {
                    $text = call_user_func($filter['callback'], $text);
                }
            }
        }
        if (!$text) {
            echo 'You sent nothing' . PHP_EOL;
            return false;
        }
        while (true) {
            switch ($type) {
                case IRC::PRIVMSG:
                    $message = IRC::PRIVMSG . ' ' . $target . ' :' . $text;
                    break;
                case IRC::MODE:
                    $message = IRC::MODE . ' ' . $target . ' ' . $text;
                    break;
                case IRC::NICK:
                    $message = IRC::NICK . ' ' . $text;
                    break;
                case IRC::TOPIC:
                    break;
                case IRC::PART:
                    $message = IRC::PART . ' ' . $target . ' :' . $text;
                    break;
                case IRC::QUIT:
                    $message = IRC::QUIT . ' :' . $text;
                    break;
                case IRC::JOIN:
                    $message = IRC::JOIN . ' ' . $text;
                    break;
                case IRC::KICK:
                    $message = IRC::KICK . ' ' . $target . ' :' . $text;
                    break;
                case IRC::NOTICE:
                    $message = IRC::NOTICE . ' ' . $target . ' :' . $text;
                    break;
                case IRC::INVITE:
                    break;
                case IRC::IDENTIFY:
                    $message = IRC::IDENTIFY . ' ' . $text;
                    break;
                case IRC::PING:
                    $message = IRC::PONG . ' ' . $text;
                    break;
                default:
                    return;
            }
            if (strlen($message) > 508) {
                $lastWhiteSpace = strrpos(substr($message, 0, 508), ' ');
                $position = ($lastWhiteSpace !== false) ? $lastWhiteSpace : strlen(substr($message, 0, 508));
                $text = substr($message, $position + 1);
                $this->buffer[] = substr($message, 0, $position);
            } else {
                $this->buffer[] = $message;
                break;
            }
        }

        while ($prio && $this->buffer) {
            $this->flushBuffer();
        }
    }

    public function getMask()
    {
        return $this->chat->getUser();
    }

    public function getType()
    {
        return $this->chat->getType();
    }

    public function getSource()
    {
        return $this->chat->getSource();
    }

    public function getOffset()
    {
        return $this->chat->getOffset();
    }

    public function getMessage()
    {
        return $this->chat->getMessage();
    }

    public function getUserNick()
    {
        return $this->chat->getUserNick();
    }

    public function getUserName()
    {
        return $this->chat->getUserName();
    }

    public function getUserHost()
    {
        return $this->chat->getUserHost();
    }

    public function startBot()
    {
        $this->setupBot();
        $this->connectToServer();
        $this->main();
    }

    private function checkStatus($data)
    {
        if (stripos($data, $error = 'Registration Timeout') !== false) {
            die($error . PHP_EOL);
        }
        if (stripos($data, 'Closing Link') !== false) {
            sleep(10 * $this->numberOfReconnects++);
            $this->connectToServer();
        }
        if (($this->getType() == IRC::RplWelcome) && ($this->numberOfReconnects > 0)) {
            $this->numberOfReconnects = 0;
        }
    }

    private function flushBuffer()
    {
        foreach ($this->buffer as $buffer) {
            $time = Config::$timePerMessage - (time() - $this->timeLastSend);
            $isTime = ($time > 0);
            if ($isTime && $this->messageToSend == 0) {
                break;
            } elseif ($isTime && $this->messageToSend-- > 0) {
                $this->sendDataToServer($buffer);
                array_shift($this->buffer);
            } elseif ($time <= 0) {
                $this->sendDataToServer($buffer);
                array_shift($this->buffer);
                $this->messageToSend = Config::$messagePerTime - 1;
                $this->timeLastSend = time();
            }
        }
    }

    private function loadModule(array $module)
    {
        $namespace = 'Module';
        foreach ($module as $moduleName) {
            Logger::add('load ' . $moduleName . ' module', Logger::INFO); //fwrite(STDOUT ,'load ' . $moduleName . ' module... ');
            $reflector = new ReflectionClass("{$namespace}\\{$moduleName}");
            $instance = $reflector->newInstance();
            $name = $reflector->getShortName();
            if (!$instance instanceof \Library\Module) {
                throw new Exception();
            }
            $this->module[$name] = $instance;
            $this->module[$name]->setIRCBot($this);
            $this->module[$name]->loadSettings();
            echo Logger::add('done', Logger::INFO);
        }
    }

    private function main()
    {
        do {
            while ($this->server->isConnected()) {
                if (!empty($this->buffer)) {
                    $this->flushBuffer();
                }
                if (!$this->server->loadData()) {
                    if ($this->channelList) {
                        Module::executeListener();
                    }
                    sleep(1);
                    continue;
                }

                foreach ($this->module as $module) {
                    $module->execute();
                    if (strpos($this->getMessage(), Config::$commandPrefix) === 0) {
                        $module->executeCommand();
                    }
                }
            }
        } while ($this->connectToServer());
        die('RIP' . PHP_EOL);
    }

    private function sendDataToServer($data)
    {
        $this->server->sendData($data);
    }

    private function setupBot()
    {
        (new Config())->simpleConfiguration();
        Logger::setLogger('debug.log', '.', Config::DEFAULT_TIMEZONE);
        $server = new Server();
        $server->getTextline();
        $server->setHost(Config::$server);
        $server->setPorts(Config::$port);
        $this->chat = $server->getTextline();
        $this->server = $server;
        $this->loadModule(Config::$modules);
        if (!$server->connect()) {
            die();
        }
    }
}
