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

class Provider implements ProviderContract
{
    protected string $baseUrl;

    protected string $feederGatewayUrl;

    protected string $gatewayUrl;

    protected Client $client;

    public function __construct(string $network)
    {
        $baseUrl = Provider::getNetworkURL($network);
        $this->baseUrl = $baseUrl;
        $this->feederGatewayUrl = "$baseUrl/feeder_gateway";
        $this->gatewayUrl = "$baseUrl/gateway";
        $this->client = new Client();
    }

    public static function getNetworkURL(string $network): string
    {
        return match ($network) {
            'mainnet' => 'https://alpha-mainnet.starknet.io',
            'testnet' => 'https://alpha4.starknet.io'
        };
    }


    /**
     * Gets the smart contract address on the goerli testnet ethereum
     * @return starknet smart contract addresses
     */
    public function getContractAddresses(): string
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_contract_addresses");
        return Arr::get($response, 'Starknet');
    }


    /**
     * Calls a function on a starknet contract
     *
     * @param array $contractTransaction
     * @param BigInteger|null $blockNumber
     * @return array the result of the function on the smart contract.
     * @throws Exception
     */
    public function callContract(array $contractTransaction, BigInteger $blockNumber = null): array
    {
        $blockNumber = is_null($blockNumber) ? 'null' : $blockNumber;

        $params = array_merge(['signature' => [], 'calldata' => []], $contractTransaction);

        $response = $this->request('POST', "$this->feederGatewayUrl/call_contract?blockId=$blockNumber", ['body' => json_encode($params)]);
        return $response['result'];
    }


    /**
     * Gets the block information
     *
     * @param BigInteger|null $blockId
     * @return array the block object { block_id, previous_block_id, state_root, status, timestamp, transaction_receipts, transactions }
     * @throws Exception
     */
    public function getBlock(BigInteger $blockId = null): array
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_block?block_id=$blockId");
        return $response;
    }


    /**
     * gets the code deployed to a contract address
     *
     * @param string $contractAddress
     * @param BigInteger|null $blockId
     * @return array containing Bytecode and ABI of compiled contract
     * @throws Exception
     */
    public function getCode(string $contractAddress, BigInteger $blockId = null): array
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_code?contractAddress=$contractAddress&block_id=$blockId");
        return $response;
    }


    /**
     * Gets the contract's storage variable at a specific key
     *
     * @param string $contractAddress
     * @param BigInteger $key
     * @param BigInteger|null $blockId
     * @return array value of the storage variable
     * @throws Exception
     */
    public function getStorageAt(string $contractAddress, BigInteger $key, BigInteger $blockId = null): array
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_storage_at?contractAddress=$contractAddress&key=$key&blockId=$blockId");
        return ["$key" => $response];
    }


    /**
     * Gets the status of a transaction.
     *
     * @param String $transactionHash
     * @return array the transaction status
     * @throws Exception
     */
    public function getTransactionStatus(String $transactionHash): array
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_transaction_status?transactionHash=$transactionHash");
        return $response;
    }


    /**
     * Gets the transaction information from a tx id.
     *
     * @param String $transactionHash
     * @return array transacton
     * @throws Exception
     */
    public function getTransaction(String $transactionHash): array
    {
        $response = $this->request('GET', "$this->feederGatewayUrl/get_transaction?transactionHash=$transactionHash");
        return $response;
    }


    /**
     * Invoke a function on the starknet contract
     *
     * @param array $transaction
     * @return array
     * @throws Exception
     */
    public function addTransaction(array $transaction): array
    {
        $signature = $transaction['type'] === 'INVOKE_FUNCTION' ? Stark::formatSignature($transaction['signature']) : null;
        $address_salt = $transaction['type'] === 'DEPLOY' ? Numbers::toHex(Numbers::toBN($transaction['contract_address_salt'])) : null;
        if ($transaction['type'] === 'INVOKE_FUNCTION') {
            $params = array_merge($transaction, ['signature' => $signature]);
        } elseif ($transaction['type'] === 'DEPLOY') {
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
     * @param array $contract
     * @param array $constructorCalldata
     * @param BigInteger $addressSalt
     * @return array confirmation of sending a transaction on the starknet contract
     */
    public function deployContract(array $contract, array $constructorCalldata, BigInteger $addressSalt): array
    {
        // @todo
    }


    /**
     * Invokes a function on starknet
     *
     * @param string $contractAddress
     * @param string $functionName
     * @param array $calldata
     * @param array|null $signature
     * @return array from addTransaction
     * @throws Exception
     */
    public function invokeFunction(string $contractAddress, string $functionName, array $calldata = [], array $signature = null): array
    {
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
     * @param string $method
     * @param string $uri
     * @param array $payload
     * @return mixed
     * @throws Exception
     */
    private function request(string $method, string $uri, array $payload = []): mixed
    {
        try {
            $response = $this->client->request($method, $uri, $payload)->getBody()->getContents();
        } catch (GuzzleException $exception) {
            throw new Exception($exception->getMessage());
        }
        return json_decode($response, true);
    }
}
