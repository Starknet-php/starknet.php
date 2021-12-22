<?php
namespace starknet\Contracts;

interface TransactionContract {


    /**
     * Returns a json string of a transaction 
     * 
     * @return String - json string of a transaction
     */
    public function toString(): String;

}