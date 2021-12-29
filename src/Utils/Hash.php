<?php
namespace starknet\Utils;
use phpseclib3\Math\BigInteger;
use starknet\Utils\Encode;
use kornrunner\Keccak;
use starknet\Utils\Numbers;
use starknet\Constants;


class Hash{

    public function __construct()
    {
    }

    public static function keccakHex(string $value): string {
        return Encode::addHexPrefix(Keccak::hash($value, 256));
    }

    public static function starknetKeccak(string $value): BigInteger{
        return Numbers::toBn(Hash::keccakHex($value))->bitwise_and(Constants::MASK_250());
    }

}



