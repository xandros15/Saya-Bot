<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-05-26
 * Time: 01:09
 */

namespace Saya\Core\Input;


interface MessageInterface extends Updater
{

    /**
     * @return string
     */
    public function getMessage() : string;

    /**
     * @return string
     */
    public function getMask() : string;

    /**
     * @return string
     */
    public function getUserName() : string;

    /**
     * @return string
     */
    public function getUserNick() : string;

    /**
     * @return string
     */
    public function getUserHost() : string;

    /**
     * @return string
     */
    public function getCommand() : string;

    /**
     * @return string
     */
    public function getSource() : string;

    /**
     * @return string
     */
    public function getParams() : string;
}