<?php

use phpseclib3\Math\BigInteger;
use starknet\Contracts\TransactionContract;


class DeployTransaction implements TransactionContract  {
    /*
    Represents a transaction in the StarkNet network that is an invocation of a Cairo contract
    function.

            export type DeployTransaction = {
        type: 'DEPLOY';
        contract_definition: CompressedCompiledContract;
        contract_address_salt: BigNumberish;
        constructor_calldata: string[];
        nonce?: BigNumberish;
};
    */
    static String $type = 'DEPLOY';

    public BigInteger $contract_address_salt;

    public array $constructor_calldata;

    public array $contract_definition;


    function __construct(String $contract_address_salt, String $constructor_calldata, array $contract_definition){

        $this->contract_address_salt = $contract_address_salt;
        $this->constructor_calldata = $constructor_calldata;
        $this->calldata = $contract_definition;

    }

    public function toString(): String {
        return 'todo';
    }

}