<?php
namespace starknet\Contracts;

use phpseclib3\Math\BigInteger;
use starknet\Contracts\ContractContract;
use starknet\Contracts\TransactionContract;

/*
[Reference] -> https://github.com/starkware-libs/cairo-lang/blob/f464ec4797361b6be8989e36e02ec690e74ef285/src/starkware/starknet/services/api/gateway/gateway_client.py
*/
interface ProviderContract {

  /**
   * Gets the smart contract address on the goerli testnet.
   *
   * @return starknet smart contract addresses
   */

  public function getContractAddresses(): string;

  
  /**
   * Calls a function on the StarkNet contract.
   *
   * @param invokeTransaction - transaction to be invoked
   * @param blockId
   * @return array the result of the function on the smart contract.
   */
  function callContract(array $contractTransactrion, BigInteger $blockNumber): array;


  /**
   * Gets the block information from a block ID.
   * 
   * @param blockId
   * @return array the block object { block_id, previous_block_id, state_root, status, timestamp, transaction_receipts, transactions }
   */
  public function getBlock(BigInteger $blockId): array;

  
  /**
   * Gets the code of a deployed contract.
   *
   * @param contractAddress
   * @param blockId
   * @return array containing Bytecode and ABI of compiled contract
   */
  public function getCode(string $contractAddress, BigInteger $blockId): array;


  /**
   * Gets the contract's storage variable at a specific key.
   * 
   * @param contractAddress
   * @param key - from getStorageVarAddress('<STORAGE_VARIABLE_NAME>') (WIP)
   * @param blockId
   * @return array value of the storage variable
   */
  public function getStorageAt(string $contractAddress, BigInteger $key, BigInteger $blockId): array;


  /**
   * Gets the status of a transaction.
   *
   * @param txHash
   * @return array the transaction status array { block_id, tx_status: NOT_RECEIVED | RECEIVED | PENDING | REJECTED | ACCEPTED_ONCHAIN }
   */
  public function getTransactionStatus(String $transactionHash): array;


  /**
   * Gets the transaction information from a tx id.
   *   
   * @param txHash
   * @return array transacton { transaction_id, status, transaction, block_id?, block_number?, transaction_index?, transaction_failure_reason? }
   */
  public function getTransaction(String $transactionHash): array;


  /**
   * Invoke a function on the starknet contract
   *   
   * @param transaction - transaction to be invoked
   * @return transaction_confirmation
   */
  public function addTransaction(TransactionContract $transaction): array;


  /**
   * Deploys a given compiled contract (json) to starknet
   *
   * @param contract - a json object containing the compiled contract
   * @param address - (optional, defaults to a random address) the address where the contract should be deployed (alpha)
   * @return a confirmation of sending a transaction on the starknet contract
   */
  public function deployContract(ContractContract $contract, array $constructorCalldata, BigInteger $addressSalt): array;


 /**
   * Invokes a function on starknet
   *
   * @param contractAddress - target contract address for invoke
   * @param entrypointSelector - target entrypoint selector for
   * @param calldata - (optional, default []) calldata
   * @param signature - (optional) signature to send along
   * @return response from addTransaction
   */
  public function invokeFunction(string $contractAddress, string $entrypointSelector, array $calldata = [], BigInteger $signature = null): array;


  public function waitForTx(BigInteger $txHash);
}