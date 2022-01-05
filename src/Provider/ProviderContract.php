<?php
namespace starknet\Provider;

use phpseclib3\Math\BigInteger;

/*
[Reference] -> https://github.com/starkware-libs/cairo-lang/blob/f464ec4797361b6be8989e36e02ec690e74ef285/src/starkware/starknet/services/api/gateway/gateway_client.py
*/
interface ProviderContract
{

  /**
   * Gets the smart contract address on the goerli testnet ethereum
   * @return starknet smart contract addresses
   */
    public function getContractAddresses(): string;


    /**
     * Calls a function on a starknet contract
     *
     * @param array $contractTransaction
     * @param BigInteger $blockNumber
     * @return array the result of the function on the smart contract.
     */
    public function callContract(array $contractTransaction, BigInteger $blockNumber): array;


    /**
     * Gets the block information
     *
     * @param BigInteger $blockId
     * @return array the block object { block_id, previous_block_id, state_root, status, timestamp, transaction_receipts, transactions }
     */
    public function getBlock(BigInteger $blockId): array;


    /**
     * gets the code deployed to a contract address
     *
     * @param string $contractAddress
     * @param BigInteger $blockId
     * @return array containing Bytecode and ABI of compiled contract
     */
    public function getCode(string $contractAddress, BigInteger $blockId): array;


    /**
     * Gets the contract's storage variable at a specific key
     *
     * @param string $contractAddress
     * @param BigInteger $key
     * @param BigInteger $blockId
     * @return array value of the storage variable
     */
    public function getStorageAt(string $contractAddress, BigInteger $key, BigInteger $blockId): array;


    /**
     * Gets the status of a transaction.
     *
     * @param string $transactionHash
     * @return array the transaction status
     */
    public function getTransactionStatus(string $transactionHash): array;


    /**
     * Gets the transaction information from a tx id.
     *
     * @param string $transactionHash
     * @return array transacton
     */
    public function getTransaction(string $transactionHash): array;


    /**
     * Invoke a function on the starknet contract
     *
     * @param array $transaction
     * @return array
     */
    public function addTransaction(array $transaction): array;


    /**
     * Deploys a given compiled contract (json) to starknet
     *
     * @param array $contract
     * @param array $constructorCalldata
     * @param BigInteger $addressSalt
     * @return array confirmation of sending a transaction on the starknet contract
     */
    public function deployContract(array $contract, array $constructorCalldata, BigInteger $addressSalt): array;


    /**
     * Invokes a function on starknet
     *
     * @param string $contractAddress
     * @param string $entrypointSelector
     * @param array $calldata
     * @param array|null $signature
     * @return array from addTransaction
     */
    public function invokeFunction(string $contractAddress, string $entrypointSelector, array $calldata = [], array $signature = null): array;
}
