<h1 align="center">starknet.php ✨🐘</h1>
<h3 align="center">starknet.php is a PHP sdk that allows you to interact with StarkNet from a PHP application.</h2>

<p align="center">
      <a href="https://starkware.co"><img alt="starkware" src="https://img.shields.io/badge/powered_by-StarkWare-navy"></a>
      <a href="https://github.com/Starknet-php/starknet.php/blob/main/LICENSE.md"><img alt="License" src="https://img.shields.io/badge/license-MIT-black"></a>
</p>

> This project is a work-in-progress. Code and documentation are currently under development and are subject to change

## Install

>  **Requires [PHP 8.0+](https://php.net/releases/)**

Install `starknet.php` via the [Composer](https://getcomposer.org/) package manager:

```bash
composer require starknet-php/starknet.php
```


## Usage

The following code can be used with a public and private key to create a signer
```bash
$wallet_address = '0x0006be19b8a602c2013deb97e2ad12b358d2f3fb2e3d4c1e96f047cb68fd8a8' // your wallet address
$pk = '' // your private key
$network = 'testnet' // can be testnet || mainnet
$signer = new Signer($pk, $network, $wallet_address);
```
The following code can be used to interact with a contract 
```bash
$contract = '0x07394cbe418daa16e42b87ba67372d4ab4a5df0b05c6e554d158458ce245bc10' // the contract address to interact with
$method = Stark::getSelectorFromName('transfer') // the method to call
$calldata = ['0x060eb76c275ce5188b9e30b212776a68e037674331437fc028b072102b6fe181', '1200000000000000000', '0'] // the parameters to call with [to, amount, max]
$signer->addTransaction([
        'type'                 => 'INVOKE_FUNCTION',
        'contract_address' => $contract,
        'entry_point_selector' => $method,
        'calldata'             => $calldata,
    ]);
```


## Testing

Tests were created using the pest testing package. To call tests run 
```bash
./vendor/bin/pest
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

## Security

 
If you discover any security related issues, please email asciik@protonmail.com instead of using the issue tracker.

 
## Credits

  
-  [Zac Whittaker][link-author]


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[link-author]: https://github.com/zascii
