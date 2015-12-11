<?php

namespace Library\Debugger;

use DateTime;
use DateTimeZone;
use Exception;
use Library\Debugger\Logger;

class Core
{
    protected
        $timezoneName = 'UTC';
    protected static
    /** @var Core */
        $logger;
    private
    /**  @var DateTime */
        $datetime,
        $filename,
        $dirname,
        $ext;

    public function setLogger($filename, $dirname, $timezone = 'UTC')
    {
        if (isset(self::$logger)) {
            return self::$logger;
        }
        $core         = new Core();
        $core->setDatetime($timezone)
            ->setDirname($dirname)
            ->setFilename($filename);
        return self::$logger = $core;
    }

    public function save($message)
    {
        return (file_put_contents($this->getPath(), $message, FILE_APPEND));
    }

    public function getPath()
    {
        return $this->dirname . DIRECTORY_SEPARATOR . $this->filename . '.' . $this->ext;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getDirname()
    {
        return $this->dirname;
    }

    public function getExt()
    {
        return $this->ext;
    }

    public function flatten(array $array, $prefix)
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
            case Logger::WARRNING: return $prefix .= ' (WARNING): ';
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