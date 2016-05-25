<?php
// Namespace

namespace Module;

use DOMXPath;
use Saya\IRC;
use Saya\Components\Helper\IRCHelper;
use Saya\Client\UrlHelper;
use RuntimeException;
use Saya\Client\Module;

/**
 *
 * @package IRCBot
 * @subpackage Listener
 * @author Remigiusz Guszkiewicz
 */
class Url extends Module
{
    const
        TYPE_PAGE = 1,
        TYPE_VIDEO = 2,
        TYPE_IMG = 3,
        TYPE_ERROR = 4,
        REGEX_ERROR = '~^HTTP/[12]\.[0-9] [54][0-9]{2}.*$~i',
        OPEN_GRAPH_QUERY = '// */meta[starts-with(@property, \'og:\')]';

    private
        $ctxDomainList = [
        'http://t.co/' => [], //twitter
        'https://t.co/' => [] //twitter
    ],
        $except = [
//'touhou.pl' => 'block',
        'myanimelist.net' => 'block',
        'mega.co.nz' => 'block',
        'mega.nz' => 'block',
        'anidb.net' => 'pass'
    ],
        $openGraph = [
        'kwejk.pl' => 'title',
        'twitch.tv' => 'description'
    ];

    public function __construct()
    {
        parent::__construct();
        libxml_use_internal_errors(true);
    }

    public static function getRegexUrl()
    {
        return '_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254' .
        '(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|' .
        '2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x' .
        '{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}' .
        '0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS';
    }

    /**
     * Main function to execute when listen even occurs
     */
    public function execute()
    {
        $this->on(IRC::PRIVMSG, 'findTitle');
    }

    protected function findTitle()
    {
        $arguments = explode(' ', $this->bot->getMessage());
        foreach ($arguments as $nr => $text) {
            if (!($url = $this->isUrl($text))) {
                continue;
            }
            $openGraph = $this->getOpenGraph($url);
            $title = $this->getTitle($url, $openGraph);
            if (!$title) {
                continue;
            }
            $title = $this->trimtext($title, 300);
            $title = $this->addNsfw($arguments, $nr, $title);
            $this->reply($title);
        }
    }

    private function trimtext($text, $lenght)
    {
        if (strlen($text) > $lenght) {
            return mb_substr($text, 0, $lenght - 5) . '(...)';
        }
        return $text;
    }

    private function addNsfw(array $arguments, $nr, $title)
    {
        return ((isset($arguments[$nr + 1])) && (mb_stripos($arguments[$nr + 1], 'nsfw') !== false)) ?
            $title .= ' [' . IRCHelper::colorText('NSFW', IRCHelper::COLOR_PINK) . ']' : $title;
    }

    private function getOpenGraph($url)
    {
        foreach ($this->openGraph as $host => $metadata) {
            if (strpos($url, $host) !== false) {
                return $metadata;
            }
        }
        return false;
    }

    private function isUrl($link)
    {
        if (strpos($link, ' ') !== false) {
            return false;
        }
        $link = rtrim($link, '\\');
        $link = ltrim($link, ':;/\\!@#$%^&*-_=+|\'"?<>[](){}.,');

        $link = (stripos($link, 'http') !== 0) ? 'http://' . $link : $link;

        foreach ($this->except as $page => $opt) {
            if (stristr($link, $page) !== false && $opt == 'pass') {
                return $link;
            }
            if (stristr($link, $page) !== false && $opt == 'block') {
                return false;
            }
        }
        return (preg_match(self::getRegexUrl(), $link)) ? $link : false;
    }

    public function getKeywords()
    {
        return array('PRIVMSG');
    }

    protected function getimagesizefromstring($string_data)
    {
        return getimagesize('data://application/octet-stream;base64,' . base64_encode($string_data));
    }

    protected function humanFilesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = (int) floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

    protected function getTitle($url, $openGraph = false)
    {
        $options = false;
        foreach ($this->ctxDomainList as $host => $hostOptions) {
            if (strpos($url, $host) === 0) {
                $options = $hostOptions;
                break;
            }
        }
        if (!($html = $this->loadStreamUrl($url, $options))) {
            return false;
        }
        $httpHeader = $this->httpHeader;
        if (!($httpHeader) || !isset($httpHeader['content-type'])) {
            return false;
        }
        foreach ($httpHeader as $key => $option) {
            if (is_array($option) || !is_int($key)) {
                continue;
            }
            if (preg_match(self::REGEX_ERROR, $option)) {
                return false;
            }
        }
        if (!empty($httpHeader['content-encoding'])) {
            $html = $this->contentEncoding($html, $httpHeader['content-encoding']);
        }
        //$size = (!empty($httpHeader['content-length'])) ?
        //    round(($httpHeader['content-length'] / 1024 / 1024), 2) . 'MB' : 0;       
        if (preg_match('/image/i', $httpHeader['content-type'])) {
            return false;
            //list($width, $height) = $this->getimagesizefromstring($html);
            //return $this->text("$width x $height in " . $this->humanFilesize(strlen($html)), self::TYPE_IMG);
        }
        if (preg_match('/(video|zip|rar|octet-stream)/i', $httpHeader['content-type'])) {
            return false;
            //return $this->text('Video file, binary file or sth else', self::TYPE_VIDEO);
        }
        if (preg_match('/audio/i', $httpHeader['content-type'])) {
            return false;
            //return $this->text("Audio file {$size}", self::TYPE_VIDEO);
        }

        if (preg_match('/charset=([^\s;]+)/i', $httpHeader['content-type'], $m)) {
            $charset = $m[1];
            unset($m);
        }

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', (isset($charset)) ? $charset : 'UTF-8');
        try {

            $doc = $this->getDOM();
            $loaded = $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            /* $doc->loadHTML('<?xml encoding="' . (isset($charset) ? $charset : 'utf-8') . '" ?>' . $html); */

            /* fallback to regex */

            if (!$loaded) {
                if (preg_match("#<title[^>]*>(.*?)</title>#Umsi", $html, $matches)) {//Umsi
                    return $this->text(html_entity_decode(
                        preg_replace('/\s+/', ' ', $matches[1])
                    ), self::TYPE_PAGE);
                }
                throw new RuntimeException(libxml_get_last_error()->message);
            }

            if ($openGraph) {
                $metadata = (new DOMXPath($doc))->query(self::OPEN_GRAPH_QUERY);
                /** @var $meta \DOMNode */
                foreach ($metadata as $meta) {
                    $property = $meta->getAttribute('property');
                    $content = $meta->getAttribute('content');
                    $rmetas[$property] = $content;
                }
                if (isset($rmetas['og:' . $openGraph])) {
                    return $this->text($rmetas['og:' . $openGraph]);
                }
            }
        } catch (RuntimeException $e) {
            return $this->text($e->getMessage(), self::TYPE_ERROR);
        }
        return (
            $doc->getElementsByTagName('title')->length > 0) ?
            $this->text(
                $doc->getElementsByTagName('title')->item(0)->nodeValue
            ) : false;
    }

    private function contentEncoding($html, $type)
    {
        switch (trim($type)) {
            case 'gzip' :
                return gzdecode($html);
            case 'deflate':
                return gzinflate($html);
            case 'compress':
                return gzuncompress($html);
            case 'identity':
                return $html;
        }
    }

    private function text($title, $type = self::TYPE_PAGE)
    {
        $text = trim($title);
        if (empty($text)) {
            return false;
        }
        switch ($type) {
            case self::TYPE_PAGE:
                return IRCHelper::colorText('Title', IRCHelper::COLOR_ORANGE) . ': ' . $text;
            case self::TYPE_IMG:
                return IRCHelper::colorText('Image', IRCHelper::COLOR_ORANGE) . ': ' . $text;
            case self::TYPE_ERROR:
                return IRCHelper::colorText('Error', IRCHelper::COLOR_RED) . ': ' . $text;
            case self::TYPE_VIDEO:
                return IRCHelper::colorText('Notice', IRCHelper::COLOR_ORANGE) . ': ' . $text;
        }
    }
}