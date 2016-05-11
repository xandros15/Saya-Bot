<?php

namespace module;

use Library\Module;
use R;
use library\Constants\IRC;
use library\helper\IRCHelper as CT;
use RedBeanPHP\OODBBean;

class Aigis extends Module
{
    const
        CLEAR_TIME = 20,
        DB_NAME = 'aigis',
        TB_NAME = 'unit',
        GALLERY_URL = 'http://aigisu.pl/image/%d';

    private
        $lastCgRequest = [],
        $lastUnitRequest = [];

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
            'arguments' => -1,
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

        parent::loadSettings($this);
    }

    protected function cg($arguments)
    {
        $request = trim($arguments[0]);

        if ($this->hasRequestDelay($request, $this->lastCgRequest)) {
            return;
        }

        $units = $this->findUnitsViaName($request);

        if (!$units) {
            $this->reply($this->getProposition($request));
            return;
        }

        if (!$this->hasOwnList($units, 'image')) {
            $this->reply("I don't have this cg (yet?).");
            return;
        }

        foreach ($units as $unit) {
            if (!$unit->ownImageList) {
                continue;
            }

            $uri = sprintf(self::GALLERY_URL, $unit->id);

            $response = sprintf(
                '%s: %s %s: %s',
                CT::textOrange('Romanji'),
                $unit->original,
                CT::textPink('NSFW'),
                $uri
            );
            $this->reply($response);
        }
    }

    protected function unit($arguments)
    {
        $request = trim($arguments[0]);

        if ($this->hasRequestDelay($request, $this->lastUnitRequest)) {
            return;
        }

        $units = $this->findUnitsViaName($request);

        if (!$units) {
            $this->reply($this->getProposition($request));
            return;
        }

        //TODO: better unique link
        $linkHolder = [];
        foreach ($units as $unit) {
            $link = (isset($arguments[1]) && strtolower($arguments[1]) == 'seesaw') ? $unit->link : $unit->linkgc;

            if (!$link || isset($linkHolder[$link])) {
                continue;
            }

            $linkHolder[$link] = true;

            $response = sprintf(
                '%s: %s %s: %s',
                CT::textOrange('Romaji'),
                $unit->original,
                CT::textOrange('Link'),
                $link
            );

            $this->reply($response);
        }
    }

    const SIMILARLY_LEVEL = 60;
    const MAX_SIMILAR = 5;

    protected function getSimilar($request)
    {
        parent::RedBeanConnect(self::DB_NAME);
        $unitNames = R::getCol('SELECT `name` FROM ' . self::TB_NAME);
        $similarNames = [];
        foreach ($unitNames as $name) {
            similar_text($request, $name, $percent);
            if ($percent >= self::SIMILARLY_LEVEL) {
                $similarNames[$name] = $percent;
            }
        }
        ksort($similarNames);

        R::close();
        $mostSimilarNames = array_slice($similarNames, 0, self::MAX_SIMILAR);
        return array_keys($mostSimilarNames);
    }

    protected function getProposition($request)
    {
        $similar = $this->getSimilar($request);

        if (!$similar) {
            $response = sprintf('Not found %s', $request);
        } elseif (count($similar) == 1) {
            $response = vsprintf('Did you mean %s?', $similar);
        } else {
            $lastElement = array_pop($similar);
            $response = sprintf('Did you mean %s or %s?',
                implode(', ', $similar),
                $lastElement
            );
        }
        return $response;

    }

    protected function findUnitsViaName($name)
    {
        parent::RedBeanConnect(self::DB_NAME);

        $limit = ' LIMIT 5 ';
        if (preg_match('/^[\x{30A0}-\x{30FF}\x{31F0}-\x{31FF}]+$/iu', $name)) {
            $units = R::findAll(self::TB_NAME, "`original` like ? {$limit}", ["%{$name}%"]);
        } else {
            $units = R::findAll(self::TB_NAME, "`name` like ? {$limit}", [$name]);
        }

        R::close();

        return $units;
    }

    protected function hasOwnList($model, $ownListName)
    {
        parent::RedBeanConnect(self::DB_NAME);
        $found = false;
        $ownListName = strtolower($ownListName);
        $ownListName = ucfirst($ownListName);
        $property = "own{$ownListName}List";
        if (is_array($model)) {
            foreach ($model as $singleModel) {
                if ($singleModel->{$property}) {
                    $found = true;
                    break;
                }
            }
        } elseif ($model instanceof OODBBean) {
            $found = (bool) ($model->{$property});
        } else {
            throw new \InvalidArgumentException('Var $model must be an array or instance of OODBean.');
        }
        R::close();

        return $found;
    }

    protected function unsetRequestDelay(array &$list)
    {
        foreach ($list as $id => $timeToClear) {
            if (time() >= $timeToClear) {
                unset($list[$id]);
            }
        }
    }

    protected function setRequestDelay($id, array &$list)
    {
        $list[$id] = time() + self::CLEAR_TIME;
    }

    protected function hasRequestDelay($id, array &$list)
    {
        $this->unsetRequestDelay($list);
        if (isset($list[$id])) {
            return true;
        }

        $this->setRequestDelay($id, $list);

        return false;
    }

    protected function getLinkToInfoDmmEvent()
    {
        $profileUrl = 'http://www.ulmf.org/bbs/member.php?u=65410';
        $dom = $this->getDOM();
        $dom->loadHTML($this->loadStreamUrl($profileUrl));
        $link = $dom->getElementById('signature');
        if ($link) {
            $link = $link->getElementsByTagName('a')
                ->item(0)
                ->getAttribute('href');
            if ($link) {
                $this->reply("Dmm event info by Petite Soeur: {$link}");
            }
        } else {
            $this->reply("Somethings wrong with dom.");
        }
    }

    const TB_NAME_OAUTH = 'oauth';
    const MAX_TOKENS = 3;

    public function generatePin()
    {
        parent::RedBeanConnect(self::DB_NAME);
        $tokens = R::find(self::TB_NAME_OAUTH);
        $response = (count($tokens) >= self::MAX_TOKENS) ? $this->trashOrReturnPin() : $this->createPin();
        $this->message($response, $this->bot->getUserNick(), IRC::NOTICE);
        R::close();
    }

    private function trashOrReturnPin()
    {
        $tokens = R::find(self::TB_NAME_OAUTH, 'ORDER BY time ASC');
        foreach ($tokens as $token) {
            if (($timeLeft = $this->checkTokenTime($token->time))) {
                return sprintf("Here your pin: '%s'. Token lifetime left:  %s", $token->pin, $timeLeft);
            } else {
                R::trash($token);
            }
        }
        return $this->createPin();
    }

    private function createPin()
    {
        $token = R::dispense(self::TB_NAME_OAUTH);
        $token->time = time() + (60 * 60);
        $token->pin = substr(md5(sha1(uniqid(time()))), 0, 8);
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