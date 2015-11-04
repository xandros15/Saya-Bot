<?php

namespace Library\BotInterface;

interface Chatter
{

    public function update();

    public function getUser();

    public function getMessage();

    public function getOffset();

    public function getSource();

    public function getType();

    public function getUserName();

    public function getUserNick();

    public function getUserHost();
}
