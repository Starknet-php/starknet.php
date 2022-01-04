<?php
namespace starknet\Helpers;

use phpseclib3\Math\BigInteger;
use starknet\Helpers\Encode;
use kornrunner\Keccak;
use starknet\Helpers\Numbers;
use starknet\Constants;


class Hash{

    public static function keccakHex(string $value): string {
        return Encode::addHexPrefix(Keccak::hash($value, 256));
    }

    public static function starknetKeccak(string $value): BigInteger{
        return Numbers::toBn(Hash::keccakHex($value))->bitwise_and(Constants::MASK_250());
    }

    public static function perdesenHash(array $dataArray){
        $ecPoints = ellipticCurve::constantPoints();
        $point = $ecPoints[0];
        for ($i = 0; $i < sizeof($dataArray); $i++){
            $x = Numbers::toBN($dataArray[$i]);
            assert($x->compare(Constants::ZERO()) > 0 || $x->equals(Constants::ZERO()) && $x->compare(Numbers::toBN(Encode::addHexPrefix(Constants::FIELD_PRIME))) < 0, "Invalid input $x");
            for ($j = 0; $j < 252; $j++){
                $pt = $ecPoints[2 + $i * 252 + $j];
                assert(!$point->getX()->eq($pt->getX()));
                $val = (int) $x->bitwise_and(Constants::ONE())->toString();
                if($val !== 0){
                    $point = $point->add($pt);
                }
                $x = $x->bitwise_rightShift(1);
            }
        }
        return Encode::removeHexLeadingZero($point->getX()->toString(16));
    }

    public static function hashArrayElements(array $data){
        $merged = array_merge($data, [sizeof($data)]);
        return array_reduce($merged ,fn($x, $y) => self::perdesenHash([$x, $y]), 0);
    }

    public static function hashCallData(array $calldata){
        return self::hashArrayElements($calldata);
    }

    public static function hashMessage(string $account, string $to, string $selector, array $calldata, string $nonce){
        ini_set('memory_limit', '-1');
        $hashedCallData = self::hashCallData($calldata);
        return self::hashArrayElements([$account, $to, $selector, $hashedCallData, $nonce]);
    }
}



