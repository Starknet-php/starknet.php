<?php

use starknet\Provider\Provider;
use phpseclib3\Math\BigInteger;

beforeEach(function () {
    $this->provider = new Provider('testnet');
});

it('can get a contract address', function () {
    $this->assertEquals($this->provider->getContractAddresses(), "0xde29d060D45901Fb19ED6C6e959EB22d8626708e");
});


it('can call a contract and return a valid response', function () {
});

it('can get the current block and information', function () {
    $response = $this->provider->getBlock();
    expect($response)->toBeArray()->toHaveKeys(["parent_block_hash", "transaction_receipts", "state_root", "transactions", "status", "block_hash", "timestamp", "block_number"]);
});

it('can get the code deployed in a contract', function () {
    $response = $this->provider->getCode('0x163a1542a64402ffc93e39a4962eec51ce126f2e634631d3f1f6770a76e3a61');
    expect($response)->toBeArray()->toHaveKeys(["bytecode", "abi"]);
});

it('can get a storage variable in a contract located at a specific key', function () {
    $response = $this->provider->getStorageAt('0x163a1542a64402ffc93e39a4962eec51ce126f2e634631d3f1f6770a76e3a61', new BigInteger(0), new BigInteger(870));
    expect($response)->toBeArray()->toHaveKey(new BigInteger(0));
});

it('can get the status of a transaction', function () {
});

it('can get transaction information', function () {
});

it('can call function in a starknet contract', function () {
});

it('can deploy a contract', function () {
});


