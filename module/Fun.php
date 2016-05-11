<?php

namespace module;

use library\constants\IRC;
use library\helper\IRCHelper;
use R;
use library\Module;

class Fun extends Module
{
    const
        API_GEL = 'http://gelbooru.com/index.php?page=dapi&s=post&q=index&limit=100&',
        API_YAN = 'https://yande.re/post.xml?',
        API_SHORT = 'http://exsubs.anidb.pl/short/',
        TRIGGER_GEL = 'GEL',
        TRIGGER_NSFW = 'NSFW',
        TRIGGER_SAFE = 'SAFE',
        TRIGGER_QUES = 'QUES',
        RATING_SAFE = 's',
        RATING_NSFW = 'e',
        RATING_QUESTIONABLE = 'q';

    private
        $rateOption = [
            'NSFW' => 'rating:explicit',
            'SAFE' => 'rating:safe',
            'QUES' => 'rating:questionable'
            ],
        $api, $rate;

    public function loadSettings($object = null)
    {
        libxml_set_streams_context($this->ctx);
        libxml_use_internal_errors(true);
        $this->setCommand([
            'trigger' => 'c',
            'action' => 'c',
            'arguments' => -1,
            'determiter' => ',',
            'help' => 'Randomize arguments. Determiter is set as ",". Example: "!c apple, banana, oranges".',
            'ban'=> [
                'host' => 'FBF0D8DB.5BE561A6.7DCB3F4E.IP'
            ]

        ]);

        $this->setCommand([
            'trigger' => 'random',
            'action' => 'random',
            'arguments' => -1,
            'help' => '"!random" default search loli, but you can find anything you want. ' .
            'You can change rating by adding safe|nsfw|ques to arguments and change ' .
            'image server by adding yan (yande.re).',
            'ban' =>
            [
                'nick' => 'Thebassa'
            ]
        ]);

        $this->setCommand([
            'trigger' => 'biba',
            'reply' => 'Biba dance: https://www.youtube.com/watch?v=kpJcgkEdMRg'
        ]);

        $this->setCommand([
            'trigger' => 'maido',
            'reply' => 'Maido dance: https://www.youtube.com/watch?v=a-7_XdPktgc'
        ]);
        $this->setCommand([
            'trigger' => 'mikuluka',
            'reply' => 'https://www.youtube.com/watch?v=ZllY2wBLYN4'
        ]);

        $this->setCommand([
            'trigger' => 'rolypoly',
            'reply' => 'Roly-poly: http://www.youtube.com/watch?v=3Xolk2cFzlo'
        ]);

        $this->setCommand([
            'trigger' => 'weaboo',
            'reply' => 'Weaboo song: https://www.youtube.com/watch?v=TBfWKmRFTjM'
        ]);

        $this->setCommand([
            'trigger' => 'xandros',
            'reply' => '[9:58pm] <Inkwizytor> xandros, kup sobie slownik'
        ]);

        $this->setCommand([
            'trigger' => 'cycki',
            'action' => 'cycki'
        ]);
        $this->setCommand([
            'action' => function () {
                $this->user->mode($this->bot->getSource(),'+b', 'Thebassa');
                $this->user->kick($this->bot->getSource(), 'Thebassa', 'nie jestem zawiedziona');
            },
            'trigger' => 'baka',
            'permit' => true,
        ]);



        parent::loadSettings($this);
    }

    protected function cycki()
    {
        $stream = $this->loadStreamUrl('http://api.oboobs.ru/noise/1/');
        $json   = json_decode($stream);
        if (!empty($json[0])) {
            $json    = $json[0];
            $message = IRCHelper::colorText('Tits', IRCHelper::COLOR_ORANGE) . ': ';
            $message .= IRCHelper::colorText('NSFW', IRCHelper::COLOR_PINK) . ' ';
            $message .= 'http://media.oboobs.ru/' . $json->preview;
        } else {
            $message = 'Can\'t get api';
        }
        $this->reply($message);
    }

    protected function c(array $arguments = [])
    {
        if (count(array_unique(array_map('trim', $arguments))) >= 2) {
            $this->reply(trim($arguments[mt_rand(0, count($arguments) - 1)]));
        } else {
            $this->reply('Wrong arguments. Type "!Help c" to help');
        }
    }

    protected function random(array $arguments = [])
    {
        $arguments = $this->setApi($arguments);
        $arguments = $this->setRate($arguments);
        if (!$arguments) {
            return false;
        }



        $tags = array_map('strtolower', array_merge($arguments, [$this->rate]));
        $url  = $this->api . 'tags=' . trim(implode('+', $tags));
        $html = $this->loadStreamUrl($url);
        if (!$html) {
            return $this->reply('I can\'t open api url.');
        }
        $doc = $this->getDOM();
        if (!$doc->loadHTML($html)) {
            return $this->reply('I can\'t open api url.');
        }

        if ($doc->getElementsByTagName('posts')->length == 0 ||
            empty($doc->getElementsByTagName('posts')
                    ->item(0)
                    ->getAttribute('count'))) {
            return $this->reply('Sorry, I didn\'t find ' . trim(implode(' ', $arguments)) . '.');
        }
        $count = $doc->getElementsByTagName('posts')
            ->item(0)
            ->getAttribute('count');

        $random = mt_rand(0, $count - 1);
        $pid    = floor($random / 100);

        switch ($this->api) {
            case self::API_GEL:
                $url .= '&json=1&pid=' . $pid;
                break;
            case self::API_YAN:
                $url = str_ireplace('.xml', '.json', $url) . '&limit=100&page=' . $pid;
                break;
        }
        $newHtml = $this->loadStreamUrl($url);
        if (!$newHtml) {
            return $this->reply('Can\'t load html to dom.');
        }
        $image = json_decode($newHtml)[$random % 100];
        $url   = $this->shortIt($image->file_url,
            ($this->api == self::API_GEL) ?
                $image->hash : $image->md5);
        $warn  = '';
        switch ($image->rating) {
            case self::RATING_NSFW:
                $warn = IRCHelper::colorText('NSFW', IRCHelper::COLOR_PINK);
                break;
            case self::RATING_SAFE:
                $warn = IRCHelper::colorText('Safe', IRCHelper::COLOR_GREEN);
                break;
            case static::RATING_QUESTIONABLE:
                $warn = IRCHelper::colorText('Questionable', IRCHelper::COLOR_ORANGE);
                break;
        }
        $tag = (!empty($arguments)) ? ' ' . trim(implode(' ', $arguments)) : '';
        $this->reply("Random{$tag}: {$warn} {$url} (from {$count} pic)");
    }

    private function shortIt($url, $md5)
    {
        if (!self::API_SHORT) {
            return $url;
        }

        parent::RedBeanConnect('short');
        if (($fetch = @R::findOne('short_url', '`title` = ?', [$md5]))) {
            return self::API_SHORT . $fetch->keyword;
        }
        $timestamp = date('Y-m-d H:i:s');
        do {
            $uniq = '';
            $base = strtolower(hash('sha256', uniqid()));
            while (strlen($uniq) < 8) {
                $uniq .= substr($base, (int) -(strlen($base) / (1 + strlen($uniq))), 1);
            }
        } while (@R::findOne('short_url', '`keyword` = ?', [$uniq]));
        $sql = 'INSERT INTO `short`.`short_url` ';
        $sql .="(`keyword`, `url`, `title`, `timestamp`, `ip`, `clicks`) ";
        $sql .= "VALUES ('{$uniq}', '{$url}', '{$md5}', '{$timestamp}', '";
        $sql .= getHostByName(getHostName()) . "', '0')";
        R::exec($sql);
        R::close();
        return 'http://exsubs.anidb.pl/short/' . $uniq;
    }

    private function setApi(array $arguments)
    {
        $key = array_search(self::TRIGGER_GEL, array_map('strtoupper', $arguments));
        if ($key !== false) {
            $this->api = self::API_GEL;
            unset($arguments[$key]);
        } else {
            $this->api = self::API_YAN;
        }
        return $arguments;
    }

    private function setRate(array $arguments)
    {
        $this->rate = '-' . $this->rateOption[self::TRIGGER_NSFW];
        if (!empty($arguments)) {
            $upperArguments = array_map('strtoupper', $arguments);
            foreach ($this->rateOption as $option => $value) {
                if (($key = array_search($option, $upperArguments)) !== false) {
                    $this->rate = $value;
                    unset($arguments[$key]);
                    break;
                }
            }
        }
        if (empty($arguments)) {
            $this->reply('What are you looking for? Type "!random" <tags>');
            return false;
        }
        return $arguments;
    }
}