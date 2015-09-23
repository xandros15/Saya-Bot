<?php

namespace Library\Helper;

class ServerHelper
{

    /**
     * 
     * @param string $ports
     * @return array
     */
    static public function parsePorts($ports)
    {
        if (strpos($ports, ',') !== false) {
            $ports = explode(',', trim($ports, ", \t\n\r\0\x0B"));
        } elseif (strpos($ports, '-') !== false) {
            $ports = explode('-', $ports);
            if (count($ports) != 2) {
                throw new Exception('$ports is wrong variable string. Given ' . $ports);
            }
            $ports =  range(min($ports), max($ports));
        } else {
            $ports = [$ports];
        }
        return array_map('intval', $ports);
    }
}
