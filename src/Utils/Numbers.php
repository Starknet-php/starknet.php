<?php
namespace starknet\Utils;
use phpseclib3\Math\BigInteger;
use Exception;
use Hamcrest\Type\IsString;
use starknet\Utils\Encode;


class Numbers{

    public function __construct()
    {
    }

    public static function isHex(string $hex): bool {
        return str_starts_with($hex, '0x');
      }

    public static function toHex(BigInteger $n){
        return Encode::addHexPrefix($n->toHex());
        
    }

    public static function toBN(string|BigInteger|int $n, int $base = null){
        if (is_string($n) && Numbers::isHex($n) && is_null($base)){
            return new BigInteger(Encode::removeHexPrefix($n), 16);
        }
        if(is_null($base)){
            return new BigInteger($n);
        } else {
            return new BigInteger($n, $base);
        }
    }

}