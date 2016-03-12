<?php

namespace Module;

use R;
use Library\Constants\IRC;
use Library\Helper\IRCHelper;

class Aigis extends \Library\Module
{
    const
        CLEAR_TIME = 60 * 1,
        DB_NAME = 'aigis',
        TB_NAME = 'unit',
        GALLERY_URL = 'http://aigisu.pl/image/%d';

    private
        $lastRequest = [],
        $unitList    = [];

    public function loadSettings($object = null)
    {
        $this->setCommand([
            'trigger' => 'pin',
            'action' => 'generatePin',
            'arguments' => 0,
            'channels' => ['#aigis']
        ]);
        $this->setCommand([
            'trigger' => 'unit',
            'action' => 'unit',
            'determiter' => ' ',
            'arguments' => 1,
            'channels' => ['#aigis'],
            'help' => 'Type "!unit {unit_name}" to get link to information about this unit.',
        ]);
        $this->setCommand([
            'trigger' => 'cg',
            'action' => 'cg',
            'determiter' => ' ',
            'arguments' => 1,
            'channels' => ['#aigis'],
            'help' => 'Type "!cg {unit_name}" to get link to unit gallery.',
        ]);
        $this->setCommand([
            'trigger' => 'dmm',
            'action' => 'getLinkToInfoDmmEvent',
            'arguments' => 0,
            'channels' => ['#aigis'],
            'help' => 'Type "!dmm" to get link to info about current dmm event.',
        ]);
        $this->setCommand([
            'trigger' => 'setup',
            'action' => 'setup',
            'arguments' => 0,
            'channels' => ['#aigis'],
            'permit' => true
        ]);

        parent::loadSettings($this);
    }

    protected function cg($arguments)
    {
        $request = trim($arguments[0]);
        $request = rtrim($request, '!@#$%^&*()_-+={}[]:;\'"\\|<>,./?');
        parent::RedBeanConnect(self::DB_NAME);
        $units   = R::findAll(self::TB_NAME, '`name` like ?', [$request]);
        if ($units) {
            foreach ($units as $unit) {
                if (!$unit->ownImageList) {
                    continue;
                }
                $text = IRCHelper::colorText('Romaji', IRCHelper::COLOR_ORANGE) . ': ' . $unit->original;
                $text .= ' ' . IRCHelper::colorText('NSFW', IRCHelper::COLOR_PINK) . ': ' . sprintf(self::GALLERY_URL,
                        $unit->id);
                $this->reply($text);
            }
        }
        (isset($text)) || $this->reply('Not found ' . $request);

        R::close();
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
        $limit = ' LIMIT 5 ';
        if (preg_match('/^[\x{30A0}-\x{30FF}\x{31F0}-\x{31FF}]+$/iu', $request)) {
            $units = R::findAll(self::TB_NAME, "`original` like ? {$limit}", ["%{$request}%"]);
        } else {
            $units = R::findAll(self::TB_NAME, "`name` like ? {$limit}", [$request]);
        }
        if ($units) {
            $linkHolder = [];
            foreach ($units as $unit) {
                if (!$unit->linkgc || isset($linkHolder[$unit->linkgc]) || isset($this->lastRequest[$unit->id])) {
                    continue;
                }
                $linkHolder[$unit->linkgc]    = $unit->original;
                $this->lastRequest[$unit->id] = time();

                $text = IRCHelper::colorText('Romaji', IRCHelper::COLOR_ORANGE) . ': ' . $unit->original;
                $text .= ' ' . IRCHelper::colorText('Link', IRCHelper::COLOR_ORANGE) . ': ' . $unit->linkgc;

                $this->reply($text);
            }
        } else {
            $this->reply('Not found ' . $request);
        }
        R::close();
    }

    protected function getLinkToInfoDmmEvent()
    {
        $profileUrl = 'http://www.ulmf.org/bbs/member.php?u=65410';
        $dom        = $this->getDOM();
        $dom->loadHTML($this->loadStreamUrl($profileUrl));
        $link       = $dom->getElementById('signature');
        if ($link) {
            $link = $link->getElementsByTagName('a')
                ->item(0)
                ->getAttribute('href');
            if ($link) {
                $this->reply("Dmm event info by Petite Soeur: {$link}");
            }
        }else{
            $this->reply("Somethings wrong with dom.");
        }
    }

    protected function setup()
    {
        $this->createUnitList();
        $this->insertUnits();
    }

    private function createUnitList()
    {
        $stream = $this->loadStreamUrl('http://seesaawiki.jp/aigis/d/%a5%e6%a5%cb%a5%c3%a5%c8%b0%ec%cd%f7%c9%bd');
        $doc    = $this->getDOM();

        if (!$doc->loadHTML($stream)) {
            echo 'error with load html' . PHP_EOL;
            return false;
        }
        $result = $doc->getElementById('content_block_22')->getElementsByTagName('a');
        if ($result->length) {
            for ($i = 0; $i < $result->length; $i++) {
                $http = $result->item($i)->attributes->getNamedItem('href')->nodeValue;
                if (preg_match('~^http://seesaawiki.jp/aigis/d/(.*)$~', $http, $maches)) {
                    $img      = $result->item($i)->firstChild;
                    $linkgc   = 'http://aigis.gcwiki.info/?' . $maches[1];
                    $title    = $img->attributes->getNamedItem('title');
                    $original = ($title) ? $title->nodeValue : '';
                    $icon     = $img->attributes->getNamedItem('src')->nodeValue;

                    $unit                       = [
                        'original' => $original,
                        'icon' => $icon,
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
        R::freeze();
        foreach ($this->unitList as $key => $unit) {
            if (R::findOne(self::TB_NAME, 'original = ?', [$unit['original']]) ||
                R::findOne(self::TB_NAME, 'linkgc = ?', [$unit['linkgc']])) {
                unset($this->unitList[$key]);
                continue;
            }
            $unitBean = R::dispense(self::TB_NAME);
            $unitBean->import($unit);
            R::store($unitBean);
            unset($this->unitList[$key]);
            $this->reply('Added ' . $unit['original']);
        }
        R::close();
    }
    const TB_NAME_OAUTH = 'oauth';
    const MAX_TOKENS    = 3;

    public function generatePin()
    {
        parent::RedBeanConnect(self::DB_NAME);
        $tokens   = R::find(self::TB_NAME_OAUTH);
        $response = (count($tokens) >= self::MAX_TOKENS) ? $this->trashOrReturnPin() : $this->createPin();
        $this->message($response, $this->bot->getUserNick(), IRC::NOTICE);
        R::close();
    }

    private function trashOrReturnPin()
    {
        $tokens = R::find(self::TB_NAME_OAUTH, 'ORDER BY time ASC');
        foreach ($tokens as $token) {
            if (($timeleft = $this->checkTokenTime($token->time))) {
                return sprintf("Here your pin: '%s'. Token lifetime left:  %s", $token->pin, $timeleft);
            } else {
                R::trash($token);
            }
        }
        return $this->createPin();
    }

    private function createPin()
    {
        $token        = R::dispense(self::TB_NAME_OAUTH);
        $token->time  = time() + (60 * 60);
        $token->pin   = substr(md5(sha1(uniqid(time()))), 0, 8);
        $token->token = md5(sha1(uniqid(time())));
        R::store($token);
        return "Here your pin: '{$token->pin}'";
    }

    private function checkTokenTime($time)
    {
        $left = $time - time();
        if ($left < 0) {
            return false;
        }
        return sprintf('%d min %d sec', (int) floor($left / 60), (int) $left % 60);
    }
}