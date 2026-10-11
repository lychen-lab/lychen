<?php

namespace Lychen\Test;

use Doctrine\Persistence\ManagerRegistry;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\KernelBrowser;

/**
 * The browser the API tests drive. Zenstruck instantiates it when the
 * KERNEL_BROWSER_CLASS environment variable names it (see each API's .env.test).
 */
class TestBrowser extends KernelBrowser
{
    /**
     * Foundry's persistent factories return entities that are detached from the
     * request's EntityManager (the kernel, hence the EM, is rebuilt for the browser).
     * Reload the managed instance so security->getUser() takes part in Doctrine's
     * identity map: required both when a listener persists the current user as the
     * owner of a new entity, and when a voter compares identities (e.g. the subject's
     * land against the API key's land).
     */
    public function actingAs(object $user, ?string $firewall = null): self
    {
        $doctrine = $this->client()->getContainer()->get('doctrine');
        \assert($doctrine instanceof ManagerRegistry);

        $manager = $doctrine->getManagerForClass($user::class);

        if (null !== $manager && method_exists($user, 'getId') && null !== $user->getId()) {
            $managed = $manager->find($user::class, $user->getId());

            if (null !== $managed) {
                $user = $managed;
            }
        }

        return parent::actingAs($user, $firewall);
    }

    /**
     * The APIs' PATCH operations take a JSON merge patch.
     *
     * @param HttpOptions|array<string, mixed> $options
     */
    public function patch(string $url, $options = ['json' => []]): self
    {
        $this->setDefaultHttpOptions(
            HttpOptions::create()
                ->withHeader('Content-Type', 'application/merge-patch+json')
                ->withHeader('Accept', 'application/ld+json')
        );

        return parent::patch($url, $options);
    }

    /**
     * Replace a service of the test container, typically with a mock, for the
     * requests this browser makes next.
     */
    public function addMock(string $id, object $service): self
    {
        $this->client()->getContainer()->set($id, $service);

        return $this;
    }
}
