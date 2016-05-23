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
        $prefix = self::COLOR . $this->getColor($color);
        if ($background) {
            $prefix .= ',' . $this->getColor($background);
        }

        return $prefix;
    }

    /**
     * @return string
     */
    protected function getSuffix(): string
    {
        return $this->getPrefix('normal', 'normal');
    }

    /**
     * @return array
     */
    protected function getColorList() : array
    {
        return [
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
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getColor(string $name)
    {
        $colorList = $this->getColorList();

        if ($name && !isset($colorList[$name])) {
            throw new InvalidColorException("Color {$name} doesn't exist.");
        }

        return mb_substr('0' . $colorList[$name], -2);
    }
}