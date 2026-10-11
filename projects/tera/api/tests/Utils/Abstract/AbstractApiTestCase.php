<?php

namespace App\Tests\Utils\Abstract;

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
use Lychen\Test\AbstractApiTestCase as LychenApiTestCase;

/**
 * The shared test case (Lychen\Test\AbstractApiTestCase) plus tera's own fixtures traits.
 */
abstract class AbstractApiTestCase extends LychenApiTestCase
{
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
}
