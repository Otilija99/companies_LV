<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$query = readline('Enter company name: ' );

$client = new Client();
try {
    $response = $client->request('GET', 'https://data.gov.lv/dati/lv/api/3/action/datastore_search', [
        'query' => [
            'resource_id' => '25e80bf3-f107-4ab4-89ef-251b5b9374e9',
            'q' => $query
        ],
    ]);

    $data = json_decode($response->getBody(), true);
    $companies = $data['result']['records'] ?? [];
} catch (RequestException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

$output = new ConsoleOutput();
$table = new Table($output);
$table
    ->setHeaders(['Name', 'Registration Number', 'Address'])
    ->setRows(array_map(function ($company) {
        return [
            $company['name'] ?? 'N/A',
            $company['registration_number'] ?? 'N/A',
            $company['address'] ?? 'N/A'
        ];
    }, $companies));

$table->render();
