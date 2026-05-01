<?php

namespace App\Temporal;

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;

final class TemporalClientFactory
{
    public static function create(string $address): WorkflowClientInterface
    {
        return WorkflowClient::create(ServiceClient::create($address));
    }
}
