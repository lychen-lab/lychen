<?php

namespace App\Tests\Functional;

use App\Entity\AreaProposal;
use App\Factory\AreaActivityFactory;
use App\Factory\AreaProposalFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\Update;

class AreaProposalMercureTest extends AbstractApiTestCase
{
    public function testCreatedProposalIsPublishedAsGetReturnsIt(): void
    {
        // Created once the browser has booted its kernel, so that the request's entity manager
        // manages this person when it becomes the proposal's proposer.
        $browser = $this->browser();
        $person = PersonFactory::createOne();

        $iri = $browser
            ->actingAs($person)
            ->post('/api/area_proposals', ['json' => [
                'title' => 'Jardin partagé de la Croix-Rousse',
                'description' => 'Parcelles, composteur et outils sur place.',
                'surfaceTotal' => 220,
                'surfaceToShare' => 60,
                'city' => 'Lyon',
                'altitude' => 250,
            ]])
            ->assertStatus(201)
            ->json()->decoded()['@id'];

        $update = self::assertPublishedOnce($iri);

        // The app applies this payload in place of a refetch, so it has to be the proposal
        // exactly as GET returns it. A fresh browser: the firewall is stateless, so the login
        // above only held for the POST.
        $item = $this->browser()->actingAs(PersonFactory::createOne())->get($iri)->assertStatus(200)->json()->decoded();
        self::assertSame($item, json_decode($update->getData(), true, flags: \JSON_THROW_ON_ERROR));
    }

    public function testUpdatedProposalIsPublishedWithItsNewState(): void
    {
        $iri = '/api/area_proposals/'.self::createProposal()->getUuid();
        $browser = $this->browser()->actingAs(PersonFactory::createOne());
        self::getMercureHub()->reset();

        $browser->patch($iri, ['json' => ['title' => 'Verger familial']])->assertStatus(200);

        $update = self::assertPublishedOnce($iri);
        self::assertSame('Verger familial', json_decode($update->getData(), true, flags: \JSON_THROW_ON_ERROR)['title']);
    }

    public function testRemovedProposalIsPublishedAsItsIriAlone(): void
    {
        $proposal = self::createProposal();
        $iri = '/api/area_proposals/'.$proposal->getUuid();
        self::getMercureHub()->reset();

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->remove($proposal);
        $entityManager->flush();

        $update = self::assertPublishedOnce($iri);
        self::assertSame(['@id' => $iri], json_decode($update->getData(), true, flags: \JSON_THROW_ON_ERROR));
    }

    public function testChangeMadeOutsideTheApiIsPublishedToTheSameTopic(): void
    {
        // Fixtures, commands and workers flush without a request, hence without a host to
        // build absolute URLs from: the topic must still be the one browsers subscribe to.
        $proposal = self::createProposal();

        self::assertPublishedOnce('/api/area_proposals/'.$proposal->getUuid());
    }

    /**
     * Asserts that exactly one update went out, privately, to the given topic.
     */
    private static function assertPublishedOnce(string $topic): Update
    {
        $updates = self::getMercureMessages();

        self::assertCount(1, $updates);
        self::assertSame([$topic], $updates[0]->getTopics());
        self::assertTrue($updates[0]->isPrivate(), 'Area proposals must only reach authorised subscribers.');

        return $updates[0];
    }

    private static function createProposal(): AreaProposal
    {
        AreaActivityFactory::createMany(5);
        PersonFactory::createOne();

        return AreaProposalFactory::createOne();
    }
}
