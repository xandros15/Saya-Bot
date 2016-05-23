<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-20
 * Time: 01:11
 */

namespace library\format;


Interface Format
{
    const BOLD = "\x02";
    const COLOR = "\x03";
    const ITALIC = "\x1D";
    const UNDERLINE = "\x1F";
    const REVERSE = "\x16";
    const RESET = "\x0F";

    /**
     * Format constructor.
     * @param string $input
     */
    public function __construct(string $input);

    /**
     * @return string
     */
    public function __toString() : string;
}