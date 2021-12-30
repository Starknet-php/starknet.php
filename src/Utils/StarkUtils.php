<?php
namespace starknet\Utils;
use phpseclib3\Math\BigInteger;
use Exception;
use starknet\Utils\Hash;


class StarkUtils{

    public function __construct()
    {
    }
    public static function formatSignature(array $signature): array {
        try{
            $bn_array = array_map(fn($x) => (new BigInteger($x))->toString(), $signature);
            return $bn_array;
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getSelectorFromName(string $functionName){
        return Encode::removeHexLeadingZero(Numbers::toHex(Hash::starknetKeccak($functionName)));
    }

    public static function compileCalldata(array $args){
        $mapped = array_map(function ($x) {
            if (is_array($x)) {
                return [Numbers::toBN(sizeof($x))->toString(), array_map(fn($y) => (Numbers::toBN($y))->toString(), $x)];
            } else {
                return (Numbers::toBN($x))->toString();
            }
        }, $args);
        $mapped = array_values($mapped);
        $flattened = self::flatten($mapped);
        return $flattened;
    }

    private static function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

}