<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-20
 * Time: 01:11
 */

namespace library\text;


Interface Format
{
    const PREFIX_BOLD = "\x02";
    const PREFIX_COLOR = "\x03";
    const PREFIX_ITALIC = "\x1D";
    const PREFIX_UNDERLINE = "\x1F";
    const PREFIX_REVERSE = "\x16";
    const SUFFIX = "\x0F";

    /**
     * @return string
     */
    public function __toString() : string;
}