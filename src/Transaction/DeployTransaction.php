<?php

use phpseclib3\Math\BigInteger;
use starknet\Transaction\TransactionContract;

class DeployTransaction implements TransactionContract
{
    public static String $type = 'DEPLOY';

    public String $contract_address_salt;

    public array $constructor_calldata;

    public array $contract_definition;

    private array $calldata;


    public function __construct(String $contract_address_salt, array $constructor_calldata, array $contract_definition)
    {
        $this->contract_address_salt = $contract_address_salt;
        $this->constructor_calldata = $constructor_calldata;
        $this->calldata = $contract_definition;
    }

    public function toString(): String
    {
        return 'todo';
    }
}
