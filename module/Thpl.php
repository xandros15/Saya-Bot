<?php

namespace Module;

use DateTimeZone;
use DateTime;
use library\constants\IRC;
use library\configuration as Config;
use library\helper\IRCHelper;
use stdClass;
use library\Module;

class Thpl extends Module
{
    const
        FILE_DATA_NAME = 'ThplData.json',
        FORUM_API = 'http://www.touhou.pl/4um/index.php?type=atom;action=.xml',
        SHORT_URL = 'http://4um.touhou.pl',
        API_WALL_URL = 'http://api.touhou.pl/wallpaper/random',
        URL_TO_ID_REGEX = '~^(?:.*?)4um/.*?topic,([0-9]+)\.msg([0-9]+)(?:.*?)$~',
        NEWS_TEXT_FORMAT = '7Forum: %s 3[Touhou3] 9Temat: %s %s',
        REGEX_URL_ERROR = '~^HTTP/[12]\.[0-9] [54][0-9]{2}.*$~i',
        SPELL_REGEX = '#^([a-zA-Z]{2,4}|\d{1,2}(?:[.,]\d)?)(?:[:/](\d{1,3}))?$#';

    protected
        $lastNewsTime = 0, //last msg time
        $lastPostTime = 1440769604; //last msg time
    private
        $api = 'http://api.touhou.pl/spells', //url api
        $channelPermit = ['#touhoupl', '#xandros'],
        $channelToSend = [],
        $forumTimeDelay = 60 * 30,
        $pageTimeDelay = 60 * 6,
        $forum = []; //buffer msg to send

    public function loadSettings()
    {
        $this->setCommand([
            'trigger' => 'thsp',
            'help' => 'I know some spell cards from touhou. Type "!thsp <game number or ' .
            'short name>[/ or :]<number of spell>" to get some information about it. ' .
            '"!thsp list" list all cards',
            'arguments' => 1,
            'channels' => ['#touhoupl', '#xandros'],
            'action' => 'action'
        ]);
        $this->setCommand([
            'trigger' => 'thwall',
            'help' => 'Searching best wallpapers from Touhou (I guess). '
            . 'Syntax: "!thwall [arguments|id]+". Accepts wildcards.',
            'arguments' => -1,
            'channels' => ['#touhoupl'],
            'action' => 'randomWall'
        ]);
        parent::loadSettings();
    }

    public function execute()
    {
        $this->on(IRC::PING, 'setLastForumPost');
        $this->on(null, 'sendLastForumPost', $this->forumTimeDelay);
        /* $this->on(null, '', $this->pageTimeDelay); */
    }

    public function propertyToSave()
    {
        return [
            'lastNewsTime' => $this->lastNewsTime,
            'lastPostTime' => $this->lastPostTime
        ];
    }
    protected function randomWall(array $arguments = [])
    {
        $id = [];
        if ($arguments) {
            foreach ($arguments as $argument => $value) {
                if (strpos($value, '/') !== false) {
                    $value = str_replace('/', '', $value);
                }
                if(preg_match('~^\d+$~', $value) && empty($id)) {
                    $id = $value;
                    unset($arguments[$argument]);
                    continue;
                }
                $arguments[$argument] = $value;
            }
            $arguments = implode('%20', $arguments);
        }
        $apiUrl = self::API_WALL_URL;
        $apiUrl .= ($arguments) ? '/' . $arguments : '';
        $apiUrl .= ($id) ? '/' . $id : '';
        $result = $this->loadStreamUrl($apiUrl);
        $jsonResult = json_decode($result);
        if (!$jsonResult) {
            return $this->reply('Sorry, no results.');
        }
        $data = [
            '{name}' => $jsonResult->name,
            '{resolution}' => $jsonResult->info->{0} . 'x' . $jsonResult->info->{1},
            '{url}' => $jsonResult->url,
            '{nsfw}' => $jsonResult->safe ? '' : IRCHelper::colorText('NSFW', IRCHelper::COLOR_PINK),
        ];
        $message = 'Random tapcia - {name} [{resolution}] here: {url} {nsfw}';
        $message = str_replace(array_keys($data), array_values($data), $message);
        $this->reply(trim($message));
    }

    protected function sendLastForumPost()
    {
        $this->channelToSend = array_intersect(
            $this->channelPermit, $this->getJoinedChannel()
        );

        if (!$this->channelToSend) {
            return false;
        }

        if (!$this->forum) {
            return;
        }
        $last = $this->lastPostTime;
        foreach ($this->forum as $key => $subject) {
            $msg = IRCHelper::colorText('Forum', IRCHelper::COLOR_ORANGE) . ': ' . $subject->lastPostDate . ' ';
            $msg .= IRCHelper::colorTrim('[Touhou]', IRCHelper::COLOR_GREEN) . ' ';
            $msg .= IRCHelper::colorTrim('(' . $subject->name . ')', IRCHelper::COLOR_RED) . ' ' ;
            $msg .= IRCHelper::colorText('Temat', IRCHelper::COLOR_LIGHT_GREEN) . ': ' . $subject->topic . ' ' . $subject->lastPostLink;
            $last = max([$subject->lastPostTime, $last]);
            $this->sendMessage($msg);
            unset($this->forum[$key]);
        }
        $this->lastPostTime = $last;
    }

    protected function setLastForumPost()
    {
        $xml = $this->xmlObject(self::FORUM_API);
        if (!is_object($xml)) {
            return false;
        }
        $findKeyObject = function(array $array, $id) {
            foreach ($array as $key => $object) {
                if ($object->id == $id) {
                    return $key;
                }
            }
            return false;
        };
        foreach ($xml->entry as $item) {
            $timestamp = strtotime($item->updated);
            if ($this->lastPostTime >= $timestamp) {
                continue;
            }
            if (!preg_match(self::URL_TO_ID_REGEX, $item->link->attributes()->href, $matches)) {
                return false;
            }

            $date = (new DateTime($item->updated))
                ->setTimezone(new DateTimeZone(Config::DEFAULT_TIMEZONE)); // TODO change localtime to global proprerty 
            $idSubject = (int) $matches[1];
            $idPost = (int) $matches[2];

            $keySubject = $findKeyObject($this->forum, $idSubject);

            if ($keySubject === false) {

                $subject = new stdClass();
                $subject->id = $idSubject;
                $subject->topic = str_replace('Odp: ', '', (string) $item->title);
                $subject->name = $item->author->name;
                $this->forum[] = $subject;
                end($this->forum);
                $keySubject = key($this->forum);
            }

            $this->forum[$keySubject]->lastPostLink = sprintf(self::SHORT_URL . '/%d/%d', $idSubject, $idPost);
            $this->forum[$keySubject]->lastPostDate = $date->format('d-m-Y H:i:s');
            $this->forum[$keySubject]->lastPostTime = $timestamp;
            $this->forum[$keySubject]->lastPostId = $idPost;
        }
        return true;
    }

    protected function getHelp()
    {
        if (!$json = json_decode($this->loadStreamUrl($this->api))) {
            return 'Something really wrong with api. h-collector, FIX IT!';
        }
        $text = 'I remember only those spells:';
        foreach ($json as $item => $value) {
            $text .= " {$item}:[1-{$value->total}]";
        }
        return $text;
    }

    protected function action($arguments)
    {
        if (strtoupper($arguments[0]) == 'LIST') {
            $this->reply($this->getHelp());
            return false;
        }
        if (!preg_match(self::SPELL_REGEX, $arguments[0], $matches)) {
            $this->reply($this->getHelp());
            return false;
        }
        if (empty($matches[2])) {
            $this->reply($this->getHelp());
            return false;
        }
        if (!$json = json_decode($this->loadStreamUrl(("$this->api/{$matches[1]}/" . (int) $matches[2])))) {
            $this->reply($this->getHelp());
            return false;
        }

        $string = IRCHelper::colorText('Name', IRCHelper::COLOR_ORANGE) . ': ' . $json->titleEN . ' ';
        $string .= IRCHelper::colorText('Character', IRCHelper::COLOR_ORANGE) . ': ' . $json->titleEN . ' ';
        $string .= IRCHelper::colorText('Stage', IRCHelper::COLOR_ORANGE) . ': ' . $json->titleEN;

        if (!empty($json->diff)) {
            $string .= IRCHelper::colorText(' Difficult level', IRCHelper::COLOR_ORANGE) . ': ' . $json->diff;
        }
        if (!empty($json->youtube)) {
            $string .= IRCHelper::colorText('YT', IRCHelper::COLOR_RED) . ': https://youtu.be/' . $json->youtube;
        }
        $this->reply($string);
    }

    /**
     * 
     * @param string $url
     * @return object
     * 
     */
    private function xmlObject($url)
    {
        $html = $this->loadStreamUrl($url);
        $xml = simplexml_load_string($html);
        return ($xml) ? $xml : false;
    }

    /**
     * send msg to #channel on network
     * 
     * @return void
     */
    private function sendMessage($message)
    {
        foreach ($this->channelToSend as $channel) {
            $this->message($message, $channel);
        }
    }
}
