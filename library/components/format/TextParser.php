<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-23
 * Time: 18:34
 */

namespace library\format;


class TextParser extends Text
{
    public function toColorParse(){
        $this->formatted = (string) (new ColorParser($this->formatted))->parse();
        return $this;
    }
}