<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-20
 * Time: 13:08
 */

namespace Saya\Components\Format;


class ColorParser extends Color
{
    const COLOR_REGEX = '/@(\w+)(?:,@(\w+))?{([^{}]+)}/';

    /**
     * Coloring text by parser
     * Example input: @ red{this text will be red} and @ blue,@ pink{this text will be blue on ping background}
     *
     * @return ColorParser
     */
    public function parse() : self
    {

        $callback = function (array $matches) {
            list($match, $color, $background, $text) = $matches;
            try {
                return $this->getPrefix($color, $background) . $text . $this->getSuffix();
            } catch (InvalidColorException $exception) {
                return $match;
            }
        };

        $this->formatted = preg_replace_callback(self::COLOR_REGEX, $callback, $this->formatted);

        return $this;
    }
}