<?php
namespace starknet\Signer;

use starknet\Provider\Provider;
use starknet\Helpers\Numbers;
use starknet\Helpers\Stark;
use starknet\Helpers\Encode;
use starknet\Helpers\ellipticCurve;
use starknet\Helpers\Hash;

class Signer extends Provider
{
    private string $pk;
    public string $address;

    public function __construct(string $pk, string $network, string $address)
    {
        $this->pk = $pk;
        $this->address = $address;
        parent::__construct($network);
    }

    public function addTransaction(array $transaction): array
    {
        // if nonce set
        if (!in_array('nonce', array_keys($transaction))) {
            $response = parent::callContract(['contract_address' => $this->address,
                'entry_point_selector' => Stark::getSelectorFromName('get_nonce')]);
            $nonceBn = Numbers::toBN($response[0]);
        } else {
            $nonceBn = Numbers::toBN($transaction['nonce']);
        }

        $calldataDecimal = array_map(function ($x) {
            return Numbers::toBN($x)->toString();
        }, $transaction['calldata']);

        $messageHash = Encode::addHexPrefix(Hash::hashMessage(
            $this->address,
            $transaction['contract_address'],
            $transaction['entry_point_selector'],
            $calldataDecimal,
            $nonceBn->toString()
        ));

        $signature = ellipticCurve::sign($this->pk, $messageHash);

        $calldataReq = [$transaction['contract_address'], $transaction['entry_point_selector'], (string) sizeof($calldataDecimal), $calldataDecimal, $nonceBn->toString()];
        $calldataReq = array_map(fn ($x) => Numbers::toBN($x)->toString(), Stark::flatten($calldataReq));

        $response = parent::addTransaction([
            'type' => 'INVOKE_FUNCTION',
            'entry_point_selector' => Stark::getSelectorFromName('execute'),
            'calldata' => $calldataReq,
            'contract_address' => $this->address,
            'signature' => $signature
        ]);
        return array($response);
    }
}
