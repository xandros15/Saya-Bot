<?php

namespace library\Debugger;

use DateTime;
use DateTimeZone;
use Exception;
use library\debugger\Logger;

class Core
{
    const
        ERROR = 1,
        WARNING = 2,
        INFO = 3,
        SUCCESS = 4;

    /** @var string */
    protected $timezoneName = 'UTC';

    /** @var Core */
    protected static $logger;

    /**  @var DateTime */
    private $datetime;

    /** @var string */
    private $filename;

    /** @var string */
    private $dirname;

    /** @var string */
    private $ext;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function add($message, $type = self::INFO)
    {
        if (!self::$logger) {
            throw new Exception('No configured logger');
        }
    }

    public static function setLogger($filename, $dirname, $timezone = 'UTC')
    {
        if (isset(self::$logger)) {
            return self::$logger;
        }
        $core = new Core();

        $core->setDatetime($timezone)
            ->setDirname($dirname)
            ->setFilename($filename);
        return self::$logger = $core;
    }

    protected function save($message)
    {
        return (file_put_contents($this->getPath(), $message, FILE_APPEND));
    }

    protected function getPath()
    {
        return $this->dirname . DIRECTORY_SEPARATOR . $this->filename . '.' . $this->ext;
    }

    protected function getFilename()
    {
        return $this->filename;
    }

    protected function getDirname()
    {
        return $this->dirname;
    }

    protected function getExt()
    {
        return $this->ext;
    }

    protected function flatten(array $array, $prefix)
    {
        $string = '';
        foreach ($array as $row) {
            if (is_array($row)) {
                $row = $this->flatten($row);
            }
            if (is_string($row)) {
                $string .= $prefix . trim($row) . PHP_EOL;
            }
        }
        return ($string) ? $string : false;
    }

    protected function setDatetime($timezoneName = 'UTC')
    {
        $timezone           = new DateTimeZone($timezoneName);
        $datetime           = new DateTime('now', $timezone);
        $this->datetime     = $datetime;
        $this->timezoneName = $timezoneName;
        return $this;
    }

    protected function getPrefix($type)
    {
        $prefix = $this->getTimestamp();

        switch ($type) {
            case Logger::ERROR: return $prefix .= ' (!ERROR): ';
            case Logger::WARNING: return $prefix .= ' (WARNING): ';
            case Logger::INFO: return $prefix .= ' (INFO): ';
            case Logger::SUCCESS: return $prefix .= ' (SUCCESS): ';
        }
    }

    protected function setFilename($file)
    {
        if (!is_file($file)) {
            $handle = fopen($file, 'w');
            fwrite($handle, '======NEW LOG FILE=====' . PHP_EOL);
            fclose($handle);
        } elseif (!is_writable($file)) {
            throw new Exception("{$file}: write: permission denied");
        }
        $fileinfo       = pathinfo($file);
        $this->ext      = $fileinfo['extension'];
        $this->filename = $fileinfo['filename'];
        return $this;
    }

    protected function setDirname($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 755, true);
        } elseif (!is_writable($dir)) {
            throw new Exception("{$dir}: open: permission denied");
        }
        $this->dirname = $dir;
        return $this;
    }

    private function getTimestamp($format = "Y-m-d H:i:s")
    {
        return $this->datetime->format('[' . $format . ']');
    }
}