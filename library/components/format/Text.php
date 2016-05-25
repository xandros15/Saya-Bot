<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-23
 * Time: 17:05
 */

namespace Saya\Components\Format;


class Text implements Format
{
    /** @var  string */
    protected $formatted;

    /** @var  string */
    protected $original;

    /**
     * Format constructor.
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->formatted = $this->original = $input;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->formatted;
    }

    /**
     * @param string $color
     * @param string $background
     * @return Text
     */
    public function toColor(string $color, string $background = '') : self
    {
        try {
            $this->formatted = (string) (new Color($this->formatted))->toColor($color, $background);
        } catch (InvalidColorException $exception) {
        }

        return $this;
    }

    /**
     * @return Text
     */
    public function toBold() : self
    {
        $this->formatted = self::BOLD . $this->formatted . self::BOLD;
        return $this;
    }

    /**
     * @return Text
     */
    public function toItalic() : self
    {
        $this->formatted = self::ITALIC . $this->formatted . self::ITALIC;
        return $this;
    }

    /**
     * @return Text
     */
    public function toUnderline() : self
    {
        $this->formatted = self::UNDERLINE . $this->formatted . self::UNDERLINE;
        return $this;
    }
}