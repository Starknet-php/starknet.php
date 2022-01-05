<?php

use starknet\Transaction\TransactionContract;

class CallFunctionTransaction implements TransactionContract
{
    /*
    Represents a transaction in the StarkNet network that is an invocation of a Cairo contract
    function.
    */
    public static String $type = 'INVOKE_FUNCTION';

    public static String $entryPointType = 'EXTERNAL';

    public String $contract_address;

    public String $entry_point_selector;

    public String $signature;

    public array $calldata;


    public function __construct(String $contract_address, String $entry_point_selector, array $calldata)
    {
        $this->contract_address = $contract_address;
        $this->entry_point_selector = $entry_point_selector;
        $this->calldata = $calldata;
    }

    public function toString(): String
    {
        return 'todo';
    }
}
