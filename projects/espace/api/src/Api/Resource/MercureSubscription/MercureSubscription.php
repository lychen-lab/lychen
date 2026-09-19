<?php

namespace App\Api\Resource\MercureSubscription;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Resource\MercureSubscription\Provider\MercureSubscriptionProvider;

/**
 * What a signed-in user needs to receive the updates this API publishes on the Mercure
 * hub: where the hub is, and a short-lived token stating which topics they may hear.
 */
#[ApiResource]
#[Get(
    uriTemplate: '/mercure_subscription',
    provider: MercureSubscriptionProvider::class,
)]
final class MercureSubscription
{
    public function __construct(
        /** Public URL of the hub, to open an EventSource on. */
        public string $hubUrl,
        /** Subscriber JWT, to send as the hub's `authorization` query parameter. */
        public string $token,
    ) {
    }
}
