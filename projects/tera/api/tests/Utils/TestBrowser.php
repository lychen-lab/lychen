<?php

namespace App\Tests\Utils;

use PHPUnit\Framework\MockObject\MockObject;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;

class TestBrowser extends KernelBrowser
{
    /**
     * Foundry's PersistentObjectFactory returns entities that are detached from the
     * request's EntityManager (the kernel/EM is rebuilt for the browser). Reload the
     * managed instance so security->getUser() participates in Doctrine's identity map —
     * required both for owner persistence (LandLinkOwnerListener) and for identity-based
     * voter checks (e.g. LandVoter comparing the subject's land to the api key's land).
     */
    public function actingAs(object $user, ?string $firewall = null): self
    {
        $manager = $this->client()->getContainer()->get('doctrine')->getManager();

        if (method_exists($user, 'getId')
            && null !== $user->getId()
            && !$manager->getMetadataFactory()->isTransient($user::class)) {
            $managed = $manager->find($user::class, $user->getId());

            if (null !== $managed) {
                $user = $managed;
            }
        }

        return parent::actingAs($user, $firewall);
    }

    public function patch(string $url, $options = ['json' => []]): self
    {
        $this->setDefaultHttpOptions(
            HttpOptions::create()->withHeader('Content-Type', 'application/merge-patch+json')->withHeader('Accept',
                'application/ld+json')
        );

        return parent::patch($url, $options);
    }

    public function addMock(string $id, MockObject $serviceMock): self
    {
        $this->client()->getContainer()->set($id, $serviceMock);
        return $this;
    }
}
