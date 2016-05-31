<?php

namespace Saya\Core;

use Saya\Core\Client\Module;
use Saya\Core\Input\MessageInterface;
use Saya\Core\Output\Request;
use Saya\Core\Server\Server;
use Saya\Core\Input\Message;
use Saya\Core\Configuration\Configuration as Config;
use Saya\Components\Filter;
use ReflectionClass;
use Exception;
use Saya\Components\Logger\Logger;

class Bot
{
    public
        $channelList = [],
        /**
         * @var $module Module
         */
        $module = [];
    /**
     * @var MessageInterface
     */
    private $chat;

    /**
     * @var Server
     */
    private $server;
    private $numberOfReconnects = 0;

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

    public function getMask()
    {
        return $this->chat->getMask();
    }

    public function getType()
    {
        return $this->chat->getCommand();
    }

    public function getSource()
    {
        return $this->chat->getSource();
    }

    public function getOffset()
    {
        return $this->chat->getParams();
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
        if (($this->getType() == IRC::RPL_WELCOME) && ($this->numberOfReconnects > 0)) {
            $this->numberOfReconnects = 0;
        }
    }

    private function loadModule(array $module)
    {
        $request = new Request($this->server, $this->chat);

        foreach ($module as $moduleName) {
            Logger::add('load ' . $moduleName . ' module', Logger::INFO);
            //fwrite(STDOUT ,'load ' . $moduleName . ' module... ');
            $reflector = new ReflectionClass($moduleName);
            $instance = $reflector->newInstance();
//            $name = $reflector->getShortName();
            if (!$instance instanceof Module) {
                throw new Exception();
            }
            $this->module[$moduleName] = $instance;
            $this->module[$moduleName]->setIRCBot($this);
            $this->module[$moduleName]->setUser($request);
            $this->module[$moduleName]->loadSettings();
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
                if (!$this->server->update()) {
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
        $this->chat = $server->getTextline()->getMessage();
        $server->setHost(Config::$server);
        $server->setPorts(Config::$port);
        $this->server = $server;
        $this->loadModule(Config::$modules);
        if (!$server->connect()) {
            die();
        }
    }
}
