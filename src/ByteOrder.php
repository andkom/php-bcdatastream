<?php

declare(strict_types=1);

namespace AndKom\BCDataStream;

// determine machine byte-order
if (!defined('BIG_ENDIAN')) {
    define('BIG_ENDIAN', pack('L', 1) === pack('N', 1));
}

/**
 * Class ByteOrder
 * @package AndKom\BCDataStream
 */
class ByteOrder
{
    const BO_MACHINE = 0;
    const BO_LE = 1;
    const BO_BE = 2;

    /**
     * @param string $bytes
     * @param int $order
     * @return string
     */
    static public function convert(string $bytes, int $order): string
    {
        if ($order != static::BO_MACHINE && ($order == static::BO_BE) != BIG_ENDIAN) {
            $bytes = strrev($bytes);
        }

        return $bytes;
    }
}