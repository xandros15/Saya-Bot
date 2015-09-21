<?php

namespace Library\BotInterface;

interface Server
{

    public function connectToServer();

    public function disconnectFromServer();

    public function loadData($data);

    public function setName($name);

    public function setDns($dns);

    public function setPorts($ports);

    public function getName();

    public function getDns();

    public function getPorts();

    public function getPassword();

    public function getChat();
}
