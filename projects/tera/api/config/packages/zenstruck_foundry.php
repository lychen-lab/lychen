<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $containerConfigurator->extension('zenstruck_foundry', [
            // Foundry 3 forces both to true. Lazy objects stay off here: tests that pass a
            // factory-made entity to a repository's findOneBy() after a request find nothing
            // with them (LandMemberInvitationTest, LandTaskTest).
            'enable_auto_refresh_with_lazy_objects' => false,
            'faker' => [
                'locale' => 'fr_FR',
            ],
            'persistence' => [
                'flush_once' => true,
            ],
        ]);
    }
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('zenstruck_foundry', [
            // Foundry 3 forces both to true. Lazy objects stay off here: tests that pass a
            // factory-made entity to a repository's findOneBy() after a request find nothing
            // with them (LandMemberInvitationTest, LandTaskTest).
            'enable_auto_refresh_with_lazy_objects' => false,
            'faker' => [
                'locale' => 'fr_FR',
            ],
            'persistence' => [
                'flush_once' => true,
            ],
        ]);
    }
};
