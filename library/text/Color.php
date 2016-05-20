<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-18
 * Time: 17:01
 */

namespace library\text;

class Color implements Format
{

    /**
     * @var string
     */
    protected $output;

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->output;
    }

    /**
     * @param string $color
     * @param string $background
     * @return string
     */
    protected function getPrefix(string $color, string $background = '') : string
    {
        $prefix = self::PREFIX_COLOR . $this->getColor($color);
        if ($background) {
            $prefix .= ',' . $this->getColor($background);
        }

        return $prefix;
    }

    /**
     * @param string $name
     * @return array|string
     */
    protected function getColor($name = '')
    {
        $colorList = [
            'white' => 0,
            'black' => 1,
            'navy' => 2,
            'green' => 3,
            'red' => 4,
            'brown' => 5,
            'purple' => 6,
            'orange' => 7,
            'yellow' => 8,
            'lime' => 9,
            'cyan' => 10,
            'aqua' => 11,
            'blue' => 12,
            'pink' => 13,
            'grey' => 14,
            'silver' => 15,
            'normal' => 16
        ];

        if ($name && !isset($colorList[$name])) {
            throw new InvalidColorException("Color {$name} doesn't exist.");
        }

        return ($name) ? mb_substr('0' . $colorList[$name], -2) : $colorList;
    }
}