<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dictionary
 *
 * @author ASUS
 */

namespace Module;

class Dictionary extends \Library\IRC\Module {

     
    
    private $dictionary = [];
    private $dir = __DIR__ . '/dict/dictionary.json';

    public function __construct()
    {
        $this->setDictionary();
    }

    private function setDictionary()
    {
        if (!file_exists($this->dir))
            file_put_contents($this->dir, json_encode([]));
        $this->dictionary = json_decode(file_get_contents($this->dir), TRUE);
    }

    public function addRule($in = [])
    {
        if (empty($in))
            return;
        if (!is_array($in))
            $in = explode(' ', strtolower($in));
        else
            $in = array_map("strtolower", $in);
        array_shift($in);
        $in[1] = implode(' ',array_slice($in,1));
        $this->dictionary[$in[0]] = $in[1];
        return file_put_contents($this->dir, json_encode($this->dictionary, JSON_PRETTY_PRINT));
    }

    public function cenStr($str = '')
    {
        if (empty($str))
            return;
        print_r($this->dictionary);
        echo "\r\n";
        print_r($str);
        echo "\r\n";
        return str_ireplace(array_keys($this->dictionary), array_values($this->dictionary), $str);
    }

}
