<?php

namespace Library\Debugger;

class Logger implements \Library\BotInterface\Logger
{

    static function pushTheData($data)
    {
        if (DEBUG && $data) {
            echo $debugText = '[' . date('H:i') . '] ' . trim($data) . IRC_EOL;
            file_put_contents('debug.log', $debugText, FILE_APPEND);
        }
    }
}
