<?php

namespace Saya\Components;

use ReflectionFunction;
use SplFileObject;
use SplObjectStorage;

class FunctionHash
{
    /**
     * List of hashes
     *
     * @var SplObjectStorage
     */
    protected static $hashes = null;

    /**
     * Returns a hash for callback
     *
     * @param callable | array $callback  
     *
     * @return string
     */
    public static function from($callback)
    {
        if (!self::$hashes) {
            self::$hashes = new SplObjectStorage();
        }

        if (!isset(self::$hashes[$callback])) {
            $ref = new ReflectionFunction($callback);
            $file = new SplFileObject($ref->getFileName());
            $file->seek($ref->getStartLine() - 1);
            $content = '';
            while ($file->key() < $ref->getEndLine()) {
                $content .= $file->current();
                $file->next();
            }
            self::$hashes[$callback] = md5(json_encode(array(
                $content,
                $ref->getStaticVariables()
            )));
        }
        return self::$hashes[$callback];
    }
}
