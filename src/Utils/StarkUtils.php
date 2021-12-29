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

    /**
     * Function to get the hex selector from a given function name
     *
     * [Reference](https://github.com/starkware-libs/cairo-lang/blob/master/src/starkware/starknet/public/abi.py#L25-L26)
     * @param functionName - selectors abi function name
     * @returns hex selector of given abi function name
     */

    public static function getSelectorFromName(string $functionName){
        return Encode::removeHexLeadingZero(Numbers::toHex(Hash::starknetKeccak($functionName)));
    }

}