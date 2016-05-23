<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-20
 * Time: 13:09
 */

namespace library\format;


class ColorTricks extends Color
{
    /**
     * @return ColorTricks
     */
    public function rainbow() : self
    {
        $colorList = array_flip($this->getColorList());
        /** w/o white, black, normal, silver and grey, because these are not colors */
        $colorList = array_diff($colorList, ['white', 'black', 'normal', 'silver', 'grey']);
        shuffle($colorList);

        $text = $this->formatted;
        $output = '';

        foreach (str_split($text) as $char) {
            if (ctype_space($char)) {
                $output .= $char;
                continue;
            }

            $color = next($colorList);
            if (!$color) {
                $color = reset($colorList);
            }

            $output .= $this->getPrefix($color) . $char;
        }

        $output .= $this->getSuffix();

        $this->formatted = $output;
        
        return $this;
    }
}