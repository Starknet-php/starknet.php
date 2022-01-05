<?php
namespace starknet\Helpers;

use Exception;
use starknet\Helpers\Hash;

class Stark
{
    public static function formatSignature(array $signature): array
    {
        try {
            return array_map(fn ($x) => ($x)->toString(), $signature);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getSelectorFromName(string $functionName): string
    {
        return Encode::removeHexLeadingZero(Numbers::toHex(Hash::starknetKeccak($functionName)));
    }

    public static function compileCalldata(array $args): array
    {
        $mapped = array_map(function ($x) {
            if (is_array($x)) {
                return [Numbers::toBN(sizeof($x))->toString(), array_map(fn ($y) => (Numbers::toBN($y))->toString(), $x)];
            } else {
                return (Numbers::toBN($x))->toString();
            }
        }, $args);
        $mapped = array_values($mapped);
        return self::flatten($mapped);
    }

    public static function flatten(array $array): array
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }
}
