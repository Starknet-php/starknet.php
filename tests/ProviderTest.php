<?php

use starknet\Provider\Provider;
use phpseclib3\Math\BigInteger;
use starknet\Helpers\Stark;

beforeEach(function () {
    $this->provider = new Provider('testnet');
});

it('can get a contract address', function () {
    $this->assertEquals($this->provider->getContractAddresses(), "0xde29d060D45901Fb19ED6C6e959EB22d8626708e");
});

it('can get the current block and information', function () {
    $response = $this->provider->getBlock();
    expect($response)->toBeArray()->toHaveKeys(["parent_block_hash", "transaction_receipts", "state_root", "transactions", "status", "block_hash", "timestamp", "block_number"]);
});

it('can get the code deployed in a contract', function () {
    $response = $this->provider->getCode('0x3188903406daaaedd123598a8bd1f5dbec34720089037f4bf1473e51857e190');
    expect($response)->toBeArray()->toHaveKeys(["bytecode", "abi"]);
});

it('can get a storage variable in a contract located at a specific key', function () {
    $response = $this->provider->getStorageAt('0x163a1542a64402ffc93e39a4962eec51ce126f2e634631d3f1f6770a76e3a61', new BigInteger(0), new BigInteger(870));
    expect($response)->toBeArray()->toHaveKey(new BigInteger(0));
});

it('can get the status of a transaction', function () {
    $response = $this->provider->getTransactionStatus('0x58691cd6827e96af42f455ce7db39a2cb9e86ee6cf6bdda6e8f3126d30c46e3');
    expect($response)->toBeArray()->toHaveKeys(["tx_status", "block_hash"]);
});

it('can get transaction information', function () {
    $response = $this->provider->getTransaction('0x58691cd6827e96af42f455ce7db39a2cb9e86ee6cf6bdda6e8f3126d30c46e3');
    expect($response)->toBeArray()->toHaveKeys(["block_hash", "block_hash", "transaction_index", "transaction", ]);
});

it('can call a contract', function () {
    $response = $this->provider->callContract(['contract_address' => '0x3188903406daaaedd123598a8bd1f5dbec34720089037f4bf1473e51857e190',
        'entry_point_selector' => Stark::getSelectorFromName('balanceOf'),
        'calldata' => Stark::compileCalldata(['account' => '0x58bc9eb6f665d6745b3c92deb6929f4ab18bb791cc78d14fcfba92e4b2654f6'])]);
        expect($response)->toBeArray()->toContain("0xde0b6b3a7640000");
});

