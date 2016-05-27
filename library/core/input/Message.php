<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-25
 * Time: 21:05
 */

namespace Saya\Core\Input;

use Saya\Core\IRC;

class Message implements MessageInterface
{

    const REGEX = '~^(?::(([^@!\ ]*)(?:(?:!([^@]*))?@([^\ ]*))?)\ )?([^\ ]+)((?:\ [^:\ ][^\ ]*){0,14})(?:\ :?(.*))?$~';

    /**
     * @var Input
     */
    protected $input;

    /**
     * @var string
     */
    protected $mask;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $userNick;

    /**
     * @var string
     */
    protected $userHost;

    /**
     * @var string
     */
    protected $params;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $message;

    /**
     * Message constructor.
     * @param Input $input
     */
    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getParams() : string
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getSource() : string
    {
        //channel case
        if (preg_match('~(?:#|&)\w+~', $this->params, $channel)) {
            switch ($this->command) {
                case IRC::PRIVMSG:
                case IRC::NOTICE:
                case IRC::MODE:
                    return $channel[0];
                default:
                    break;
            }
        }

        return $this->userNick;
    }

    /**
     * @return string
     */
    public function getCommand() : string
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getMask() : string
    {
        return $this->mask;
    }

    /**
     * @return string
     */
    public function getUserName() : string
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getUserNick() : string
    {
        return $this->userNick;
    }

    /**
     * @return string
     */
    public function getUserHost() : string
    {
        return $this->userHost;
    }

    public function update()
    {
        $this->clean();
        $input = $this->input->getInput();
        if (!preg_match(self::REGEX, $input, $data)) {
            throw new MessageParseException("Can't parse data: {$input}");
        }

        $this->mask = $data[1] ?? '';
        $this->userNick = $data[2] ?? '';
        $this->userName = $data[3] ?? '';
        $this->userHost = $data[4] ?? '';
        $this->command = mb_strtoupper($data[5] ?? '');
        $this->params = mb_strtolower(trim($data[6] ?? ''));
        $this->message = $data[7] ?? '';
    }

    private function clean()
    {
        $this->mask =
        $this->userNick =
        $this->userName =
        $this->userHost =
        $this->command =
        $this->params =
        $this->message = '';
    }
}