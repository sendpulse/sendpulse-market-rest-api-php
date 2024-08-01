<!-- PROJECT LOGO -->
<br />
<div align="center">
<h3 align="center">SendPulse Market REST client library</h3>
  <p align="center">
    <br />
    <a href="https://sendpulse.ua/ru/integrations/api">API Documentation</a>
    ·    
    <a href="https://sendpulse.com/knowledge-base/app-directory/developers">Developer Documentation</a>
  </p>
</div>


A simple SendPulse Market REST client library and example for PHP.

### Requirements

- php: >=7.1.0
- ext-json: *
- ext-curl: *

### Installation

Via Composer:

```bash
composer require sendpulse/market-rest-api
```

### Example

```php
<?php

require 'vendor/autoload.php';

use Sendpulse\MarketRestApi\Client;
use Sendpulse\MarketRestApi\Storage\FileStorage;
use Sendpulse\MarketRestApi\Exception\ClientException;


$appId = '9b0f2f98-d75f-4562-887e-2b79bc8a1eee';
$appSecret = '0d028163-9c84-40e4-8508-f4927badf735';
$requestParamCode = '5869d2b87b132aa1372242f223e5381e'; //get code into request


/**
 * Get client credentials by login request
 * @link https://sendpulse.com/knowledge-base/app-directory/developers/login-flow
 */
try {
    $userCredentials = (new Client())
        ->getClientCredentialsByCode($requestParamCode, $appId, $appSecret);

    var_dump($userCredentials);
} catch (ClientException $e) {
    var_dump([
        'message' => $e->getMessage(),
        'httpCode' => $e->getCode(),
        'responseBody' => $e->getResponseBody(),
        'headers' => $e->getHeaders(),
        'curlErrors' => $e->getCurlErrors(),
    ]);
}

/**
 * List user addressbooks by client credentials
 * @link https://sendpulse.ua/ru/integrations/api/bulk-email#lists
 */
try {
    $clientId = '';
    $clientSecret = '';

    $addressbooks = (new Client())
        ->setClientCredentials($clientId, $clientSecret, new FileStorage(''))
        ->get('addressbooks', [], true);

    var_dump($addressbooks);
} catch (ClientException $e) {
    var_dump([
        'message' => $e->getMessage(),
        'httpCode' => $e->getCode(),
        'responseBody' => $e->getResponseBody(),
        'headers' => $e->getHeaders(),
        'curlErrors' => $e->getCurlErrors(),
    ]);
}

/**
 * List user addressbooks by one flow
 * @link https://sendpulse.ua/ru/integrations/api/bulk-email#lists
 */
try {
    $client = new Client();
    $userCredentials = $client->getClientCredentialsByCode($requestParamCode, $appId, $appSecret);

    $addressbooks = $client->setClientCredentials(
        $userCredentials['client_id'],
        $userCredentials['client_secret'],
        new FileStorage('')
    )->get('addressbooks', [], true);

    var_dump($addressbooks);
} catch (ClientException $e) {
    var_dump([
        'message' => $e->getMessage(),
        'httpCode' => $e->getCode(),
        'responseBody' => $e->getResponseBody(),
        'headers' => $e->getHeaders(),
        'curlErrors' => $e->getCurlErrors(),
    ]);
}

```
