<?php
namespace starknet\Transaction;

interface TransactionContract {


    /**
     * Returns a json string of a transaction 
     * 
     * @return String - json string of a transaction
     */
    public function toString(): String;

}