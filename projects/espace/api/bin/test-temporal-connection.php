#!/usr/bin/env php
<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

$address = $_SERVER['TEMPORAL_ADDRESS'] ?? getenv('TEMPORAL_ADDRESS') ?: 'temporal:7233';

echo "Connecting to Temporal at: {$address}\n";

try {
    $serviceClient = \Temporal\Client\GRPC\ServiceClient::create($address);
    $client = \Temporal\Client\WorkflowClient::create($serviceClient);

    // List namespaces as a connectivity probe
    $response = $serviceClient->ListNamespaces(new \Temporal\Api\Operatorservice\V1\ListNamespacesRequest());

    echo "Connection successful!\n";
    echo "Namespaces found:\n";
    foreach ($response->getNamespaces() as $ns) {
        echo '  - ' . $ns->getNamespaceInfo()->getName() . "\n";
    }
} catch (\Throwable $e) {
    echo "Connection FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
