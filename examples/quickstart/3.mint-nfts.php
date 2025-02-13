<?php

require __DIR__ . '/../../vendor/autoload.php';

use XRPL_PHP\Client\JsonRpcClient;
use XRPL_PHP\Core\Networks;
use XRPL_PHP\Models\Account\AccountNftsRequest;

function convertStringToHex(string $in): string {
    $hex = '';
    foreach(str_split($in) as $char) {
        $hex .= dechex(ord($char));
    }

    return strtoupper($hex);
}

print_r(PHP_EOL . "--- NFT Testnet example ---" . PHP_EOL);

$testnetUrl = Networks::getNetwork('testnet')['jsonRpcUrl'];
$client = new JsonRpcClient($testnetUrl);

$standbyWallet = $client->fundWallet($client);
sleep(2); // TODO: Check for race condition in fundWallet()
print_r("Created standby wallet - address: {$standbyWallet->getAddress()} seed: {$standbyWallet->getSeed()}" . PHP_EOL);

$standbyTokenUrl = 'ipfs://bafybeigdyrzt5sfp7udm7hu76uh7y26nf4dfuylqabf3oclgtqy55fbzdi'; // Seems to be hardcoded in the examples
$standbyFlags = 8; // Sets the tsTransferable flag
$standbyTransferFee = 1000; // 1% Fee

$transactionBlob = [
    "TransactionType" => "NFTokenMint",
    "Account" => $standbyWallet->getClassicAddress(),
    "URI" => convertStringToHex($standbyTokenUrl),
    "Flags" => (int) $standbyFlags,
    "TransferFee" => (int) $standbyTransferFee,
    "NFTokenTaxon" => 8 //Required, but if you have no use for it, set to zero.
];

$txResult = $client->submitAndWait(
    transaction: $transactionBlob,
    autofill: true,
    wallet: $standbyWallet,
);

$nftsRequest = new AccountNftsRequest(account: $standbyWallet->getClassicAddress());
$nftsResponse = $client->request($nftsRequest)->wait();

print_r("AccountNftsRequest result:" . PHP_EOL);
print_r($nftsResponse->getResult());

