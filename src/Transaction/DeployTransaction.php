<?php

use phpseclib3\Math\BigInteger;
use starknet\Transaction\TransactionContract;


class DeployTransaction implements TransactionContract  {
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