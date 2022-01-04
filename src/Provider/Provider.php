<?php
/**
* @author Zac Whittaker <asciik@protonmail.com>
*/

namespace starknet\Provider;

use starknet\Provider\ProviderContract;
use phpseclib3\Math\BigInteger;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use Pest\Support\Arr;
use starknet\Helpers\Numbers;
use starknet\Helpers\Stark;

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
     * Gets the smart contract address on the goerli testnet ethereum
     * @return starknet smart contract addresses
     */
    public function getContractAddresses(): string{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_contract_addresses");
        return Arr::get($response, 'Starknet');
    }


    /**
     * Calls a function on a starknet contract 
     *
     * @param invokeTransaction - transaction to be invoked
     * @param blockId
     * @return array the result of the function on the smart contract.
     */
    public function callContract(array $contractTransaction, BigInteger $blockNumber = null): array{

        $blockNumber = is_null($blockNumber) ? 'null' : $blockNumber;

        $params = array_merge( ['signature' => [], 'calldata' => []], $contractTransaction);

        $response = $this->request('POST', "$this->feederGatewayUrl/call_contract?blockId=$blockNumber", ['body' => json_encode($params)]);
        return $response['result'];
    }


    /**
     * Gets the block information
     * 
     * @param blockId
     * @return array the block object { block_id, previous_block_id, state_root, status, timestamp, transaction_receipts, transactions }
     */
    public function getBlock(BigInteger $blockId = null): array{
        $response = $this->request('GET', "$this->feederGatewayUrl/get_block?block_id=$blockId");
        return $response;
    }

    
    /**
     * gets the code deployed to a contract address
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
     * Gets the contract's storage variable at a specific key
     * 
     * @param contractAddress
     * @param key 
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
     * @return array the transaction status
     */
    public function getTransactionStatus(String $transactionHash): array{

        $response = $this->request('GET', "$this->feederGatewayUrl/get_transaction_status?transactionHash=$transactionHash");
        return $response;
    }


    /**
     * Gets the transaction information from a tx id.
     *   
     * @param txHash
     * @return array transacton
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
    public function addTransaction(array $transaction): array{

        $signature = $transaction['type'] === 'INVOKE_FUNCTION' ? Stark::formatSignature($transaction['signature']) : null;
        $address_salt = $transaction['type'] === 'INVOKE_FUNCTION' ? Numbers::toHex(Numbers::toBN($transaction['contract_address_salt'])) : null;
        if ($transaction['type'] === 'INVOKE_FUNCTION'){
            $params = array_merge($transaction, ['signature' => $signature]);
        } else if($transaction['type'] === 'DEPLOY'){
            $params = array_merge($transaction, [$address_salt]);
        } else {
            throw new Exception('invalid transaction type');
        }
        var_dump($params);

        $response = $this->request('POST', "$this->gatewayUrl/add_transaction", ['json' => $params]);
        return $response;
    }


    /**
     * Deploys a given compiled contract (json) to starknet
     *
     * @param contract - a php array containing the compiled contract
     * @param address - (optional, defaults to a random address) the address where the contract should be deployed (alpha)
     * @return a confirmation of sending a transaction on the starknet contract
     */
    public function deployContract(array $contract, array $constructorCalldata, BigInteger $addressSalt): array{
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
    public function invokeFunction(string $contractAddress, string $functionName, array $calldata = [], array $signature = null): array{
        return $this->addTransaction([
            'type' => 'INVOKE_FUNCTION',
            'contract_address' => $contractAddress,
            'entry_point_selector' => Stark::getSelectorFromName($functionName),
            'calldata' => $calldata,
            'signature' => $signature
        ]);
    }

    /**
     * Used for handling external requests to the starknet sequencer 
     * 
     * @param method - the method being called {GET | POST}
     * @param uri - the endpoint being called
     * @param payload - the parameters of the request
     */
    private function request(string $method, string $uri, array $payload = []){
        try{
            $response = $this->client->request($method, $uri, $payload)->getBody()->getContents();
        } catch (GuzzleException $exception){
            throw new Exception($exception->getMessage());
        }
        return json_decode($response, true);
    }

    
}