<?php
namespace starknet\Helpers;

use phpseclib3\Math\BigInteger;
use starknet\Helpers\Encode;

class Numbers
{
    public static function isHex(string $hex): bool
    {
        return str_starts_with($hex, '0x');
    }

    public static function toHex(BigInteger $n): string
    {
        return Encode::addHexPrefix($n->toHex());
    }

    public static function toBN(string|BigInteger|int $n, int $base = null): BigInteger
    {
        if (is_string($n) && Numbers::isHex($n) && is_null($base)) {
            return new BigInteger(Encode::removeHexPrefix($n), 16);
        }
        if (is_null($base)) {
            return new BigInteger($n);
        } else {
            return new BigInteger($n, $base);
        }
    }
}
