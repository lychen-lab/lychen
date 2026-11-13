<?php

namespace App\Tests\Functional;

use App\Factory\PersonFactory;
use App\Tests\AreaListingCase;
use App\Workflow\AreaLifecycle;
use Lychen\Test\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;

/**
 * AreaLifecycle called as the future Temporal activities will: on an entity, with no
 * request and no signed-in user.
 */
class AreaLifecycleTest extends AbstractApiTestCase
{
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testListingGoesThroughItsLifecycle(AreaListingCase $case): void
    {
        $lifecycle = self::getContainer()->get(AreaLifecycle::class);
        $listing = $case->create(PersonFactory::createOne(), 'draft');
        self::assertTrue($lifecycle->isEditable($listing));

        $lifecycle->submit($listing);
        self::assertSame($case->pending, $listing->getPlace());
        self::assertFalse($lifecycle->isEditable($listing));

        $lifecycle->reject($listing, 'Précisez la surface.');
        self::assertSame('draft', $listing->getPlace());
        self::assertSame('Précisez la surface.', $listing->getRejectionReason());

        $lifecycle->submit($listing);
        self::assertNull($listing->getRejectionReason());

        $lifecycle->accept($listing);
        self::assertSame($case->public, $listing->getPlace());
        self::assertNull($listing->getArchivedAt());

        $lifecycle->archive($listing);
        self::assertSame('archived', $listing->getPlace());
        self::assertNotNull($listing->getArchivedAt());
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testRefusedRejectionLeavesTheListingUntouched(AreaListingCase $case): void
    {
        $lifecycle = self::getContainer()->get(AreaLifecycle::class);
        $listing = $case->create(PersonFactory::createOne(), $case->public);

        try {
            $lifecycle->reject($listing, 'Trop tard.');
            self::fail('A public listing cannot be rejected.');
        } catch (NotEnabledTransitionException) {
            self::assertSame($case->public, $listing->getPlace());
            self::assertNull($listing->getRejectionReason());
        }
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testRejectionWithoutReasonIsBlockedByTheWorkflow(AreaListingCase $case): void
    {
        $lifecycle = self::getContainer()->get(AreaLifecycle::class);
        $listing = $case->create(PersonFactory::createOne(), $case->pending);

        $this->expectException(NotEnabledTransitionException::class);

        $lifecycle->reject($listing, '  ');
    }
}
