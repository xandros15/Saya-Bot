<?php

namespace Saya\Core\Configuration;

class Configuration
{
    const APPLICATION_NAME = 'Saya Bot';
    const DEFAULT_TIMEZONE = 'Europe/Warsaw';
    const VERSION = '=== alpha === https://github.com/xandros15/Saya-Bot';

    public static
        $serverName = '',
        $server = '',
        $port = 0,
        $personal = null,
        $permit = [],
        $channels = [],
        $logFolder = '',
        $modules = [];
    public static
        $commandPrefix = '!',
        $timePerMessage = 1.6,
        $messagePerTime = 1,
        $floodMsg = 1;
    private static
        $nick = '';

    public function simpleConfiguration()
    {
        $this->setDefine();
        static::changeAplicationName(self::APPLICATION_NAME);
        set_time_limit(0);
        if (DEBUG) {
            ini_set('display_errors', 'on');
        }
        global $argv;
        if (is_array($arguments = $this->getArguments($argv))) {
            extract($arguments);
        }

        $nameBotConfig = (!empty($server)) ? $server : DEFAULT_CONFIG;
        if (file_exists(ROOT_DIR . '/settings/config.' . $nameBotConfig . '.json')) {
            $config = file_get_contents(ROOT_DIR . '/settings/config.' . $nameBotConfig . '.json');
        } else {
            die('Not found config file');
        }
        //todo throw error if config is no corrected
        $config = json_decode($config);
        static::$serverName = (string) strtolower($nameBotConfig);
        static::$server = (string) $config->server;
        static::$port = (string) $config->port;
        static::$personal = (object) $config->personal;
        static::$permit = (array) $config->permit;
        static::$channels = (array) $config->channels;
        static::$logFolder = (string) $config->logFolder;
        static::$modules = (array) $config->modules;
        static::setNick($config->personal->nick);
        static::changeAplicationName(static::$personal->nick . ' - ' . static::$server);
        date_default_timezone_set(self::DEFAULT_TIMEZONE);
    }

    public static function getServerName()
    {
        return static::$serverName;
    }

    public static function getNick()
    {
        return static::$nick;
    }

    public static function setNick($nick)
    {
        static::$nick = $nick;
    }

    private function setDefine()
    {
        define('IRC_EOL', "\r\n");
        define('DEBUG', 1);
        define('DEFAULT_CONFIG', 'irchighway');
        define('SETTING_FOLDER', 'settings');
    }

    private static function changeAplicationName($name)
    {
        if (php_sapi_name() === 'cli' && version_compare(PHP_VERSION, '5.5.0', '>=')) {
            cli_set_process_title($name);
        }
    }

    private function getArguments($arguments)
    {
        $parse = [];
        foreach ($arguments as $argument) {
            if (strpos($argument, ':') !== false) {
                $column = explode(':', $argument, 2);
                $parse[trim($column[0])] = trim($column[1]);
            }
        }
        return ($parse) ? $parse : false;
    }
}
