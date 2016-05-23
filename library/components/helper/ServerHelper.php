<?php

namespace library\helper;

use Exception;

class ServerHelper
{

    /**
     * @param string $ports
     * @return array
     * @throws Exception
     */
    static public function parsePorts($ports)
    {
        if (!is_string($ports)) {
            throw new Exception('$ports isn\'t string.', 0);
        }
        $finalPorts = [];
        $portsArray = (strpos($ports, ',') !== false) ? explode(',', trim($ports, ", \t\n\r\0\x0B")) : [$ports];
        foreach ($portsArray as $port) {
            if (strpos($port, '-') !== false) {
                $port = explode('-', $port);
                if (count($ports) != 2) {
                    throw new Exception("ports is wrong variable string. Given {$ports}");
                }
                $finalPorts = array_merge($finalPorts, range(min($port), max($port)));
            } else {
                $finalPorts[] = $port;
            }
        }
        return array_map('intval', $finalPorts);
    }
}
