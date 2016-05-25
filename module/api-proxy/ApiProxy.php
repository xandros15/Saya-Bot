<?php

namespace ApiProxy;

use Saya\Client\Module;

class ApiProxy extends Module
{
    const
        URL_API = 'api.touhou.pl/external',
        QUERY_REGEX = '~-(\w+?):([^ ]+)~';

    public function loadSettings($object = null)
    {
        $this->setCommand([
            'trigger' => 'api',
            'action' => 'getResponseFromQuery',
            'arguments' => -1,
            'channels' => ['#touhoupl'],
            'permit' => true
        ]);
        parent::loadSettings($this);
    }

    public function saveSettings()
    {
        return parent::saveSettings();
    }

    //> !api param:value query1 query2
    protected function getResponseFromQuery(array $arguments = [])
    {
        if (!$arguments) {
            return;
        }
        $trzeciAgrumentDoCallbacka = implode(' ', $arguments);
        $params = [];
        $query  = preg_replace_callback(self::QUERY_REGEX,
            function($m) use (&$params) {
            $params[$m[1]] = $m[2];
        },$trzeciAgrumentDoCallbacka);

        $url = self::URL_API . '?' . http_build_query(['q' => trim($query)] + $params);

        $response = json_decode($this->loadStreamUrl($url), true);

        if ($response) {
            $this->reply($this->flat($response));
        }
    }

    private function flat(array $array)
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->flat($value);
            } elseif (is_string($value)) {
                $string .= $key . ':' . $value . ' ';
            }
        }
        return rtrim($string, ' ');
    }
}