<?php
namespace starknet\Provider;

use starknet\Contracts\ProviderContract;
use phpseclib3\Math\BigInteger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use starknet\Contracts\ContractContract;
use starknet\Contracts\TransactionContract;
use Exception;
use Pest\Support\Arr;

class Provider implements ProviderContract{

    protected string $baseUrl;

    protected string $feederGatewayUrl;

    protected string $gatewayUrl;

    protected Client $client;

    function __construct(string $network){
        $baseUrl = Provider::getNetworkURL($network);
        $this->baseUrl = $baseUrl;
        $this->feederGatewayUrl = "$baseUrl/feeder_gateway";
        $this->gatewayUrl = "$baseUrl/gateway";
        $this->client = new Client();

    }

    static function getNetworkURL(string $network): string{
        return match($network){
            'mainnet' => 'https://alpha-mainnet.starknet.io',
            'testnet' => 'https://alpha4.starknet.io'
        };
    }


    /**
     * Gets the smart contract address on the goerli testnet.
     *
     * @return starknet smart contract addresses
     */

    public function getContractAddresses(): string{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_contract_addresses");
        return Arr::get($response, 'Starknet');
    }


    /**
     * Calls a function on the StarkNet contract.
     *
     * @param invokeTransaction - transaction to be invoked
     * @param blockId
     * @return array the result of the function on the smart contract.
     */
    function callContract(array $contractTransactrion, BigInteger $blockNumber): array{
        // @todo
    }


    /**
     * Gets the block information from a block ID.
     * 
     * @param blockId
     * @return array the block object { block_id, previous_block_id, state_root, status, timestamp, transaction_receipts, transactions }
     */
    public function getBlock(BigInteger $blockId = null): array{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_block?block_id=$blockId");
        return $response;
    }

    
    /**
     * Gets the code of a deployed contract.
     *
     * @param contractAddress
     * @param blockId
     * @return array containing Bytecode and ABI of compiled contract
     */
    public function getCode(string $contractAddress, BigInteger $blockId = null): array{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_code?contractAddress=$contractAddress&block_id=$blockId");
        return $response;
    }


    /**
     * Gets the contract's storage variable at a specific key.
     * 
     * @param contractAddress
     * @param key - from getStorageVarAddress('<STORAGE_VARIABLE_NAME>') (WIP)
     * @param blockId
     * @return array value of the storage variable
     */
    public function getStorageAt(string $contractAddress, BigInteger $key, BigInteger $blockId = null): array{
        
        $response = $this->request('GET', "$this->feederGatewayUrl/get_storage_at?contractAddress=$contractAddress&key=$key&blockId=$blockId");
        return ["$key" => $response];
    }


    /**
     * Gets the status of a transaction.
     *
     * @param txHash
     * @return array the transaction status array { block_id, tx_status: NOT_RECEIVED | RECEIVED | PENDING | REJECTED | ACCEPTED_ONCHAIN }
     */
    public function getTransactionStatus(String $transactionHash): array{

        $response = $this->request('GET', "$this->feederGatewayUrl/get_transaction_status?transactionHash=$transactionHash");
        return $response;
    
    }


    /**
     * Gets the transaction information from a tx hash.
     *   
     * @param txHash
     * @return array transacton { transaction_id, status, transaction, block_id?, block_number?, transaction_index?, transaction_failure_reason? }
     */
    public function getTransaction(String $transactionHash): array{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_transaction?transactionHash=$transactionHash");
        return $response;
    }


    /**
     * Invoke a function on the starknet contract
     *   
     * @param transaction - transaction to be invoked
     * @return transaction_confirmation
     */
    public function addTransaction(TransactionContract $transaction): array{
        // @todo
    }


    /**
     * Deploys a given compiled contract (json) to starknet
     *
     * @param contract - a json object containing the compiled contract
     * @param address - (optional, defaults to a random address) the address where the contract should be deployed (alpha)
     * @return a confirmation of sending a transaction on the starknet contract
     */
    public function deployContract(ContractContract $contract, array $constructorCalldata, BigInteger $addressSalt): array{
        // @todo
    }


    /**
     * Invokes a function on starknet
     *
     * @param contractAddress - target contract address for invoke
     * @param entrypointSelector - target entrypoint selector for
     * @param calldata - (optional, default []) calldata
     * @param signature - (optional) signature to send along
     * @return response from addTransaction
     */
    public function invokeFunction(string $contractAddress, string $entrypointSelector, array $calldata = [], BigInteger $signature = null): array{
        // @todo
    }


    public function waitForTx(BigInteger $txHash): void{
        // @todo
    }

    private function request(string $method, string $uri, array $payload = []){
        try{
            $response = $this->client->request($method, $uri, $payload)->getBody()->getContents();
            
        } catch (GuzzleException $exception){
            throw new Exception($exception->getMessage());
        }
        return json_decode($response, true);
    }

    
}