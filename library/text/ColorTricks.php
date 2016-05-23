<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-20
 * Time: 13:09
 */

namespace library\text;


class ColorTricks extends Color
{
    /**
     * @param string $input
     * @return ColorTricks
     */
    public function rainbow(string $input) : self
    {
        $colorList = array_flip($this->getColorList());
        /** w/o white, black, normal, silver and grey, because these are not colors */
        $colorList = array_diff($colorList, ['white', 'black', 'normal', 'silver', 'grey']);
        shuffle($colorList);

        foreach (str_split($input) as $char) {
            if (ctype_space($char)) {
                $this->output .= $char;
                continue;
            }

            $color = next($colorList);
            if (!$color) {
                $color = reset($colorList);
            }

            $this->output .= $this->getPrefix($color) . $char;
        }
        $this->output .= $this->getSuffix();

        return $this;
    }
}