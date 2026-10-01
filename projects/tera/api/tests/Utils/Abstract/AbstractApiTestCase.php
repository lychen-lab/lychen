<?php

namespace App\Tests\Utils\Abstract;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Utils\Trait\ContextTrait;
use App\Tests\Utils\Trait\LandApiKeyTrait;
use App\Tests\Utils\Trait\LandAreaTrait;
use App\Tests\Utils\Trait\LandCultivationPlanTrait;
use App\Tests\Utils\Trait\LandDealTrait;
use App\Tests\Utils\Trait\LandGreenhouseTrait;
use App\Tests\Utils\Trait\LandHarvestEntryTrait;
use App\Tests\Utils\Trait\LandMemberInvitationTrait;
use App\Tests\Utils\Trait\LandMemberTrait;
use App\Tests\Utils\Trait\LandProposalTrait;
use App\Tests\Utils\Trait\LandRequestTrait;
use App\Tests\Utils\Trait\LandRoleTrait;
use App\Tests\Utils\Trait\LandTaskTrait;
use App\Tests\Utils\Trait\LandTrait;
use App\Tests\Utils\Trait\PersonApiKeyTrait;
use App\Tests\Utils\Trait\PersonTrait;
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

    use LandTrait;
    use PersonTrait;
    use LandAreaTrait;
    use LandGreenhouseTrait;
    use LandCultivationPlanTrait;
    use LandRoleTrait;
    use LandMemberTrait;
    use LandMemberInvitationTrait;
    use LandTaskTrait;
    use LandRequestTrait;
    use LandProposalTrait;
    use LandDealTrait;
    use ContextTrait;
    use PersonApiKeyTrait;
    use LandApiKeyTrait;
    use LandHarvestEntryTrait;

    protected function browser(array $options = [], array $server = []): KernelBrowser
    {
        return $this->baseKernelBrowser($options, $server)
            ->setDefaultHttpOptions(
                HttpOptions::create()
                    ->withHeader('Accept', 'application/ld+json')
                    ->withHeader('Content-Type', 'application/ld+json')
            );
    }

    #[NoReturn]
    protected function commitAndDie(): void
    {
        StaticDriver::commit();
        exit;
    }
}
