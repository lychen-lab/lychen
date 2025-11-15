<?php

namespace App\Tests\Utils\Abstract;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use JetBrains\PhpStorm\NoReturn;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class AbstractApiTestCase extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;
    use HasBrowser {
        browser as baseKernelBrowser;
    }

    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
                    ->withHeader('Content-Type', 'application/ld+json')
            );
    }

    #[NoReturn] protected function commitAndDie(): void
    {
        StaticDriver::commit();
        exit;
    }
}
