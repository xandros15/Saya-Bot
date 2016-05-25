<?php

namespace Saya\Components;

use Saya\Components\Helper\IRCHelper;
use Symfony\Component\Process\Process;

class Filter
{

    public function filterList()
    {
        return [
            [
                'callback' => 'htmlspecialchars_decode',
            ],
            [
                'callback' => [__CLASS__, 'kanjiToRomaji'],
                'channelBlock' => ['#touhoupl'],
            ],
            [
                'callback' => [__CLASS__, 'customeFilter'],
            ],
            [
                'callback' => [__CLASS__, 'multispace'],
            ],
            [
                'callback' => [__CLASS__, 'interbang'],
            ],
            [
                'callback' => [__CLASS__, 'me'],
            ],
            [
                'callback' => [__CLASS__, 'twitch'],
                'serverAllow' => ['twitch']
            ],
        ];
    }

    public static function customeFilter($input)
    {
        $prop = [
            ' :' => ':',
            ' ;' => ';',
            ' ?' => '?',
            ' .' => '.',
            ' ,' => ',',
            ' !' => '!',
            '( ' => '(',
            ' )' => ')',
            'gintama' => 'kintama',
            '::' => '::',
            'hosi' => '★',
            'kurwa' => 'k***a',
            'chuj' => 'c**j',
            'cipa' => 'c**a',
            'fuck' => 'f**k',
            'pierdole' => 'kierdole',
        ];

        return str_ireplace(array_keys($prop), array_values($prop), $input);
    }

    public static function multispace($input)
    {
        while (strpos($input, '  ') !== false) {
            $input = str_replace('  ', ' ', $input);
        }
        return $input;
    }

    public static function interbang($input)
    {
        if (preg_match_all('/(!|\?){3,}/', $input, $m)) {
            foreach ($m[0] as $i) {
                $input = str_replace($i, substr($i, 0, 1)
                    . substr($i, strlen($i) / 2 + 1, 1)
                    . substr($i, strlen($i) - 1, 1), $input);
            }
        }
        return $input;
    }

    public static function me($input)
    {
        if (strpos($input, '/me') === 0) {
            $input = 'ACTION' . str_replace('/me', '', $input) . '';
        }
        return $input;
    }

    public static function kanjiToRomaji($input, &$returnCode = '')
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $input;
        }
        $japanese = "/([\x{3000}-\x{303f}\x{3040}-\x{309f}\x{30a0}-\x{30ff}\x{ff00}-\x{ff9f}\x{4e00}-\x{9faf}\x{3400}-\x{4dbf}])+/iu";
        preg_match_all($japanese, $input, $matches);
        if (empty($matches[0]) || !is_array($matches[0])) {
            return $input;
        }
        $placeholders = [ 'in' => $matches[0], 'out' => []];
        $uniq = uniqid();
        $kakasi = 'kakasi -i euc -w | kakasi -i euc -Ha -Ka -Ja -Ea -ka';
        $process = new Process($kakasi);
        $words = implode($uniq, $placeholders['in']);
        $convertedWords = mb_convert_encoding($words, 'eucjp', 'utf-8');
        $process->setInput($convertedWords);
        $process->run();
        if (!$process->isSuccessful()) {
            return IRCHelper::colorText('ERROR', IRCHelper::COLOR_RED) . ': can\'t run kakasi';
        }
        $romaji = $process->getOutput();
        $placeholders['out'] = explode($uniq, $romaji);
        $input = str_replace($placeholders['in'], $placeholders['out'], $input);
        return $input;
    }

    public static function twitch($input)
    {
        return preg_replace('~(?:1[0-3]|[1-9])(.*)~', '$1', $input);
    }
}
