<?php

use starknet\Provider\Provider;
use starknet\Signer\Signer;
use starknet\Helpers\Stark;

beforeEach(function () {
    $this->provider = new Provider('testnet');
    $this->signer = new Signer('', 'testnet', '0x58bc9eb6f665d6745b3c92deb6929f4ab18bb791cc78d14fcfba92e4b2654f6');
});


it('can send eth', function (){
    $response = $this->signer->addTransaction([
        'type' => 'INVOKE_FUNCTION',
        'contract_address' => '0x03188903406daaaedd123598a8bd1f5dbec34720089037f4bf1473e51857e190',
        'entry_point_selector' => Stark::getSelectorFromName("approve"),
        'calldata' => ['0x58bc9eb6f665d6745b3c92deb6929f4ab18bb791cc78d14fcfba92e4b2654f6', '100']
    ]);
})->skip();