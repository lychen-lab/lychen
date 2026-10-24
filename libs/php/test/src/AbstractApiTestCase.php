<?php

namespace Lychen\Test;

use ApiPlatform\Test\ApiTestCase;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

/**
 * Base class of the APIs' functional and security tests: a database reset per test,
 * Foundry factories, the Messenger test transports and a TestBrowser speaking JSON-LD.
 *
 * An API extends it to add its own test traits (see tera's AbstractApiTestCase).
 */
abstract class AbstractApiTestCase extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;
    use HasBrowser {
        browser as baseKernelBrowser;
    }

    /**
     * @param array<string, mixed> $options
     * @param array<string, mixed> $server
     */
    protected function browser(array $options = [], array $server = []): TestBrowser
    {
        $browser = $this->baseKernelBrowser($options, $server);

        if (!$browser instanceof TestBrowser) {
            throw new \LogicException(\sprintf('KERNEL_BROWSER_CLASS must name %s or a subclass: set it in the API\'s .env.test.', TestBrowser::class));
        }

        $browser->setDefaultHttpOptions(
            HttpOptions::create()
                ->withHeader('Accept', 'application/ld+json')
                ->withHeader('Content-Type', 'application/ld+json')
        );

        return $browser;
    }

    /**
     * Commit the transaction DAMA keeps open around the test and stop, to inspect the
     * database state of a failing test by hand.
     */
    protected function commitAndDie(): never
    {
        StaticDriver::commit();
        exit;
    }
}
