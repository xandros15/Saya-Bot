<?php

namespace Saya\Components\Helper;

/* @todo
 * change colors method
 * 1. method chaining
 *      $helper->orange('Romaji')->gray(": $unit->original")->pink('NSFW')->gray(':)
 * 2. nazewnictwo
 *
 * 3. parser
 *       "@red{Romaji}: {$unit->original} @pink{NSFW}: "
 */
class IRCHelper
{
    const
        COLOR_BLACK = 1,
        COLOR_DARK_BLUE = 2,
        COLOR_GREEN = 3,
        COLOR_RED = 4,
        COLOR_BROWN = 5,
        COLOR_PURPLE = 6,
        COLOR_ORANGE = 7,
        COLOR_YELLOW = 8,
        COLOR_LIGHT_GREEN = 9,
        COLOR_GREEN_BLUE = 10,
        COLOR_AQUA = 11,
        COLOR_BLUE = 12,
        COLOR_PINK = 13,
        COLOR_DARK_GRAY = 14,
        COLOR_LIGHT_GRAY = 15,
        COLOR_WHITE = 16;

    public static function __callStatic($name, $arguments)
    {
        if (substr($name, 0, 4) == 'text') {

            $colorConst = 'self::COLOR_' . strtoupper(StringHelper::fromCamelCase(substr($name, 4)));
            if (!defined($colorConst)) {
                throw new \BadMethodCallException();
            }

            return self::colorText($arguments[0], constant($colorConst));
        }

        throw new \BadMethodCallException();

    }


    /**
     *
     * @param string $text
     * @param int $color
     * @return string
     */
    static public function colorText($text, $color)
    {
        $start = chr(3) . mb_substr(0 . $color, -2);
        $end = chr(15);
        return $start . $text . $end;
    }

    /**
     *
     * @param string $text
     * @param int $color
     * @param int $numberOfBorderLetters
     * @return string
     */
    static public function colorTrim($text, $color, $numberOfBorderLetters = 1)
    {
        if ($numberOfBorderLetters < 1) {
            $numberOfBorderLetters = 1;
        }
        if (($textLen = strlen($text)) < ($numberOfBorderLetters * 2)) {
            $numberOfBorderLetters = $textLen / 2;
        }
        $start = chr(3) . mb_substr(0 . $color, -2);
        $end = chr(15);
        $text = substr_replace($text, $end, $numberOfBorderLetters, 0);
        $text = substr_replace($text, $start, ($numberOfBorderLetters * -1), 0);
        return $start . $text . $end;
    }
}
