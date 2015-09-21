<?php

namespace Module;

use R;
use Library\Constants\IRC;
use Library\Helper\IRCHelper;

class Aigis extends \Library\Module
{
    const
        CLEAR_TIME = 60 * 1,
        DB_NAME = 'xandrosmaker_cba_pl',
        TB_NAME = 'units';

    private
        $lastRequest = [],
        $unitList = [];

    public function loadSettings()
    {
        $this->setCommand([
            'trigger' => 'unit',
            'action' => 'unit',
            'determiter' => ' ',
            'arguments' => 1,
            'channels' => ['#aigis'],
            'help' => 'Type "!unit {unit_name}" to get link to information about this unit.',
        ]);
        $this->setCommand([
            'trigger' => 'setup',
            'action' => 'setup',
            'arguments' => 0,
            'channels' => ['#aigis'],
            'permit' => true
        ]);

        parent::loadSettings();
    }

    protected function unit($arguments)
    {
        $request = trim($arguments[0]);
        $request = rtrim($request, '!@#$%^&*()_-+={}[]:;\'"\\|<>,./?');
        foreach ($this->lastRequest as $id => $timeToClear) {
            if ((time() - $timeToClear) >= self::CLEAR_TIME) {
                unset($this->lastRequest[$id]);
            }
        }
        parent::RedBeanConnect(self::DB_NAME);
        if (preg_match('/^[\x{30A0}-\x{30FF}\x{31F0}-\x{31FF}]+$/iu', $request)) {
            $units = R::findAll(self::TB_NAME, '`orginal` like ?', ["%{$request}%"]);
        } else {
            $units = R::findAll(self::TB_NAME, '`name` like ?', [$request]);
        }
        if ($units) {
            $linkHolder = [];
            foreach ($units as $unit) {
                if (!$unit->linkgc || isset($linkHolder[$unit->linkgc]) || isset($this->lastRequest[$unit->id])) {
                    continue;
                }
                $linkHolder[$unit->linkgc] = $unit->orginal;
                $this->lastRequest[$unit->id] = time();
                $text = IRCHelper::colorText('Romanji', IRCHelper::COLOR_ORANGE) . ': ' . $unit->orginal;
                $text .= ' ' . IRCHelper::colorText('Link', IRCHelper::COLOR_ORANGE) . ': ' . $unit->linkgc;
                $this->reply($text);
            }
        } else {
            $this->reply('Not found ' . $request);
        }
        R::close();
    }

    protected function setup()
    {
        $this->createUnitList();
        $this->createTable();
        $this->insertUnits();
    }

    private function createUnitList()
    {
        $stream = $this->loadStreamUrl('http://seesaawiki.jp/aigis/d/%a5%e6%a5%cb%a5%c3%a5%c8%b0%ec%cd%f7%c9%bd');
        $doc = $this->getDOM();

        if (!$doc->loadHTML($stream)) {
            echo 'error with load html' . PHP_EOL;
            return false;
        }
        $result = $doc->getElementById('content_block_22')->getElementsByTagName('a');
        if ($result->length) {
            for ($i = 0; $i < $result->length; $i++) {
                $http = $result->item($i)->attributes->getNamedItem('href')->nodeValue;
                if (preg_match('~^http://seesaawiki.jp/aigis/d/(.*)$~', $http, $maches)) {
                    $img = $result->item($i)->firstChild;
                    $linkgc = 'http://aigis.gcwiki.info/?' . $maches[1];
                    $title = $img->attributes->getNamedItem('title');
                    $orginal = ($title) ? $title->nodeValue : '';
                    $unit = [
                        'orginal' => $orginal,
                        'icon' => $img->attributes->getNamedItem('src')->nodeValue,
                        'link' => $http,
                        'linkgc' => $linkgc,
                    ];
                    $this->unitList[$maches[1]] = $unit;
                }
            }
        } else {
            echo 'not find' . PHP_EOL;
        }
    }

    private function insertUnits()
    {
        parent::RedBeanConnect(self::DB_NAME);
        foreach ($this->unitList as $unit) {
            if (!$last = R::findOne(self::TB_NAME, 'link = ?', [$unit['link']])) {
                $unitBean = R::dispense(self::TB_NAME);
                $unitBean->import($unit);
                R::store($unitBean);
                $this->reply('Added ' . $unit['orginal']);
            }
        }
        R::close();
    }

    private function createTable()
    {
        parent::RedBeanConnect(self::DB_NAME);
        $sql = 'CREATE TABLE IF NOT EXISTS `' . self::TB_NAME . '`(';
        $sql .='`id` int(10) NOT NULL AUTO_INCREMENT,';
        $sql .='`name` varchar(25),';
        $sql .='`orginal` varchar(45) NOT NULL,';
        $sql .='`icon` varchar(100) NOT NULL,';
        $sql .='`link` varchar(100) NOT NULL,';
        $sql .='`linkgc` varchar(100) NOT NULL,';
        $sql .='PRIMARY KEY (`id`)';
        $sql .=') ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;';
        $sql = trim($sql);
        R::exec($sql);
        R::close();
    }
}
