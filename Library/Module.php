<?php

namespace Library;

use DOMDocument;
use Exception;
use Library\Helper\UrlHelper;
use Library\FunctionHash;
use Library\Configuration as Config;
use Library\Constants\IRC;
use R;
use ReflectionClass;

abstract class Module
{
    const
        DB_CONFIG_NAME = 'db.json',
        SETTING_PATHNAME = 'Module/settings';
    const
        CHECK_BAN = 'BAN',
        CHECK_CHAN = 'CHAN',
        CHECK_PERMIT = 'PERMIT';
    const
        FILE_SAVE = 'SAVE',
        FILE_LOAD = 'LOAD';

    protected
    /** @var \Library\Bot */
        $bot = null,
        $commands = [],
        $executeTime = [],
        $httpHeader = [];
    static protected $dbConfig = null;

    public function __toString()
    {
        return get_class($this);
    }

    public function __construct()
    {
        $this->setCtx();
    }

    public function __destruct()
    {
        $this->saveSettings();
    }

    public function execute()
    {
        return false;
    }

    public function executeCommand()
    {
        if (!$this->commands) {
            return;
        }
        foreach ($this->commands as $command) {
            $message = strtolower($this->bot->getMessage());
            $trigger = substr(explode(' ', $message, 2)[0], 1);
            if (!$trigger || $trigger != $command['trigger']) {
                continue;
            }
            if (!$this->checkCommand($command, self::CHECK_CHAN)) {
                continue;
            }
            if (!$this->checkCommand($command, self::CHECK_PERMIT)) {
                $this->reply('I don\'t listen to you! Baaka!');
                continue;
            } elseif ($this->checkCommand($command, self::CHECK_BAN)) {
                $this->reply('You scary me ._.');
                continue;
            }
            if ($command['reply']) {
                $this->reply($command['reply']);
            }
            if ($command['notice']) {
                $this->message($command['notice'], $this->bot->getUserNick(), IRC::NOTICE);
            }
            if ($command['action'] && method_exists($this, $command['action'])) {
                if (($arguments = $this->getArguments($command)) !== false) {
                    if (count($arguments) > 0) {
                        call_user_func_array([$this, $command['action']], [$arguments]);
                    } else {
                        call_user_func([$this, $command['action']]);
                    }
                }
            }
        }
    }

    public function loadSettings($object = null)
    {
        if (!static::$dbConfig) {
            $dbConfigFile = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, SETTING_FOLDER, self::DB_CONFIG_NAME]);
            if (!file_exists($dbConfigFile)) {
                new Exception('Don\'t found a config filename:' . $dbConfigFile);
            }
            $dbConfig = static::$dbConfig = json_decode(file_get_contents($dbConfigFile));
        }
        if (!$object || !method_exists($object, 'propertyToSave')) {
            return;
        }


        $json = json_decode($this->settingFile(self::FILE_LOAD, null, $object), true);
        if (!empty($json)) {
            foreach ($json as $property => $data) {
                if (!property_exists($object, $property)) {
                    echo "Property {$property} no exists" . PHP_EOL;
                    continue;
                }
                if ($property == 'commands') {
                    foreach ($data as $command) {
                        $this->setCommand($command);
                    }
                } else {
                    $this->{$property} = $data;
                }
                echo "Loaded property {$property}" . PHP_EOL;
            }
        }
    }

    public function saveSettings()
    {
        if (!method_exists($this, 'propertyToSave')) {
            return;
        }
        $settings = $this->propertyToSave();
        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);

        return $this->settingFile(self::FILE_SAVE, $json);
    }

    public function setIRCBot(\Library\Bot $ircBot)
    {
        $this->bot = $ircBot;
    }

    protected function setCommand(array $command)
    {
        $default = [
            'trigger' => '',
            'action' => false,
            'determiter' => ' ',
            'reply' => '',
            'notice' => '',
            'arguments' => 0,
            'channels' => [],
            'permit' => false,
            'help' => '',
            'ban' => [
                'regex' => '',
                'nick' => '',
                'name' => '',
                'host' => ''
            ]
        ];
        $command = array_replace_recursive($default, $command);
        foreach ($this->bot->module as $module) {
            if (isset($module->commands[$command['trigger']])) {
                echo "Command {$command['trigger']} exists" . PHP_EOL;
                return false;
            }
        }
        $this->commands[$command['trigger']] = $command;
        echo "Command {$command['trigger']} was loaded" . PHP_EOL;
        return $command;
    }

    protected function unsetCommand($name)
    {
        foreach ($this->bot->module as $module) {
            if (isset($module->commands[$name])) {
                unset($module->commands[$name]);
                echo "Command {$name} was deleted" . PHP_EOL;
                return true;
            }
        }
        echo "Command {$name} not found" . PHP_EOL;
        return false;
    }

    protected function editCommand(array $command)
    {
        foreach ($this->bot->module as $module) {
            if (isset($module->commands[$command[$command['trigger']]])) {
                $oldCommand = $module->commands[$command['trigger']];
                $newCommand = array_replace_recursive($oldCommand, $command);
                $this->commands[$command['trigger']] = $newCommand;
                echo "Command {$command['trigger']} was edited" . PHP_EOL;
                return true;
            }
        }
        echo "Command {$command['trigger']} not found" . PHP_EOL;
        return false;
    }

    protected function getArguments($command)
    {
        $lenTrigger = strlen($command['trigger']) + strlen(Config::$commandPrefix);
        $offtext = trim(substr($this->bot->getMessage(), $lenTrigger));
        $determiter = ($command['determiter']) ? $command['determiter'] : ' ';
        if ($offtext === '') {
            $arguments = [];
        } else {
            $arguments = explode($determiter, $offtext);
        }
        if ((count($arguments) != $command['arguments']) && ($command['arguments'] != -1)) {
            $this->reply('Wrong count of arguments');
            $this->reply(($command['help']) ? $command['help'] : "Just type \"!{$command['trigger']}\"");
            return false;
        }
        return $arguments;
    }

    protected function message($message, $target, $type = IRC::PRIVMSG, $prio = false)
    {
        $this->bot->fillBuffer(
            $message, $type, $target, $prio
        );
    }

    protected function reply($message, $type = IRC::PRIVMSG, $prio = false)
    {
        $this->bot->fillBuffer(
            $message, $type, $this->bot->getSource(), $prio
        );
    }

    protected function setCtx($login = [])
    {
        $opts = [
            'http' =>
            [
                'timeout' => 15,
                'header' => implode("\r\n", [
                    'Accept-Language: en-US,en;q=0.8',
                    'Accept-Charset:UTF-8,*;q=0.5',
                    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) ' .
                    'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                    'Ubuntu Chromium/36.0.1985.125 ' .
                    'Chrome/36.0.1985.125 Safari/537.36'
                ]),
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false
            ]
        ];
        $opts['http']['header'] = (!empty($login)) ? 'Authorization: Basic ' .
            base64_encode(sprintf("%s:%s", $login['login'], $login['key'])) .
            "\r\n" . $opts['http']['header'] : $opts['http']['header'];

        $this->ctx = stream_context_create($opts);
    }

    protected function loadStreamUrl($url, $opts = false)
    {
        $ctx = ($opts !== false) ? stream_context_create($opts) : $this->ctx;

        $stream = @fopen($url, 'r', false, $ctx);
        if (empty($stream)) {
            return false;
        }

        if (empty($http_response_header)) {
            return false;
        }

        $this->httpHeader = UrlHelper::httpParseHeaders($http_response_header);

        $html = stream_get_contents($stream, 1024 * 1024 * 3); //max 3mb
        fclose($stream);
        return $html;
    }

    protected function getDOM()
    {
        $dom = new DOMDocument;
        $dom->recover = true;
        $dom->strictErrorChecking = false;
        return $dom;
    }

    protected function getJoinedChannel()
    {
        $channel = array_keys($this->bot->channelList);
        return (!empty($channel)) ? $channel : [];
    }

    protected function on($event, $callback, $delay = 0, array $arguments = [])
    {
        if (($this->bot->getType() != $event) && !is_null($event)) {
            return false;
        }
        if (is_array($callback) && (count($callback) == 2)) {
            $callbackId = "{$callback[0]}\\{$callback[1]}";
        } elseif ($callback instanceof \Closure) {
            $callbackId = FunctionHash::from($callback);
        } elseif (method_exists($this, $callback)) {
            $callback = [$this, $callback];
            $callbackId = "{$callback[0]}\\{$callback[1]}";
        } else {
            throw new Exception("Can't call function in {$this} class. Callback no exists or isn't callable.");
        }
        if (is_null($event)) {
            return $this->registerTimeFunction([
                    'id' => $callbackId,
                    'callback' => $callback,
                    'delay' => $delay,
                    'time' => time(),
                    'arguments' => $arguments
            ]);
        }

        if ($delay) {
            if (!isset($this->executeTime[$callbackId])) {
                $this->executeTime[$callbackId] = 0;
            }
            $timeToExecute = ((time() - $this->executeTime[$callbackId]) >= $delay);
        } else {
            $timeToExecute = true;
        }

        if ($timeToExecute) {
            call_user_func_array($callback, $arguments);
            if ($delay) {
                $this->executeTime[$callbackId] = time();
            }
            return $callbackId;
        }
    }

    protected function registerTimeFunction($functionOptions)
    {
        if (isset(static::$listener[$functionOptions['id']])) {
            return false;
        }
        $id = $functionOptions['id'];
        unset($functionOptions['id']);
        static::$listener[$id] = $functionOptions;
        return $id;
    }
    protected static $listener = [];

    public static function executeListener()
    {
        foreach (static::$listener as $id => $listener) {
            if (!$listener['delay']) {
                throw new Exception("Can't call function {$id}. Function can't have no delay and no event listener.");
            }
            if ((time() - $listener['time']) >= $listener['delay']) {
                static::$listener[$id]['time'] = time();
                call_user_func_array($listener['callback'], $listener['arguments']);
                return $id;
            }
        }
    }

    protected function checkCommand($command, $mode)
    {
        switch ($mode) {
            case self::CHECK_BAN:
                if (!$command['ban']) {
                    return true;
                }
                foreach ($command['ban'] as $ban) {
                    if (!empty($ban['regex'])) {
                        return preg_match($ban['regex'], $this->bot->getMask());
                    } elseif (!empty($ban['host'])) {
                        return ($this->bot->getUserHost() == $ban['host']);
                    } elseif (!empty($ban['nick'])) {
                        return ($this->bot->getUserNick() == $ban['nick']);
                    } elseif (!empty($ban['name'])) {
                        return ($this->bot->getUserName() == $ban['name']);
                    }
                }
                return false;
            case self::CHECK_CHAN:
                if (!$command['channels']) {
                    return true;
                }
                return ((bool) array_intersect($command['channels'], [$this->bot->getSource()]));
            case self::CHECK_PERMIT:
                if (!$command['permit']) {
                    return true;
                }
                foreach (Config::$permit as $permit) {
                    if ($this->bot->getUserHost() == $permit) {
                        return true;
                    }
                }
                return false;
        }
    }

    /**
     * 
     * @param string $dbname
     * @param boolean $frozen
     * @return void
     */
    protected static function RedBeanConnect($dbname, $frozen = true)
    {

        require_once (implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'Library', 'database', 'rb.php']));
        $dbConfig = static::$dbConfig;
        switch (strtoupper($dbConfig->type)) {
            case 'SQLITE':
                $dns = "sqlite:/tmp/{$dbname}.sqlite3";
                $dbConfig->user = $dbConfig->password = null;
                break;
            case 'MARIA':
                $dns = "mysql:host={$dbConfig->host};dbname={$dbname}";
                break;
            case 'POSTGRESQL':
                $dns = "pgsql:host={$dbConfig->host};dbname={$dbname}";
                break;
            case null:
            default :
                $dns = null;
                $dbConfig->user = $dbConfig->password = null;
        }
        if (isset(R::$toolboxes[$dbname])) {
            R::selectDatabase($dbname);
        } else {
            R::addDatabase($dbname, $dns, $dbConfig->user, $dbConfig->password, $frozen);
            R::selectDatabase($dbname);
        }
        R::setAutoResolve();
        R::fancyDebug(DEBUG);
    }

    private function settingFile($mode, $content = null, $object = null)
    {
        $object = ($object) ? $object : $this;
        $filename = (new ReflectionClass($object))->getShortName() . '.json';
        switch ($mode) {
            case self::FILE_LOAD;
                $pathname = implode(DIRECTORY_SEPARATOR, [
                    ROOT_DIR,
                    self::SETTING_PATHNAME,
                    Config::getServerName(),
                    $filename
                ]);
                if (!file_exists($pathname)) {
                    return false;
                }
                return file_get_contents($pathname);
            case self::FILE_SAVE;
                $pathname = implode(DIRECTORY_SEPARATOR, [
                    ROOT_DIR,
                    self::SETTING_PATHNAME,
                    Config::getServerName()
                ]);
                if (!is_dir($pathname)) {
                    mkdir($pathname, 0755, true);
                }
                $pathname = implode(DIRECTORY_SEPARATOR, [$pathname, $filename]);
                return file_put_contents($pathname, $content);
        }
        return false;
    }
}
