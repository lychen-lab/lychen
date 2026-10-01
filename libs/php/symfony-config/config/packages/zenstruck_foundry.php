<?php

declare(strict_types=1);

$foundry = [
    'zenstruck_foundry' => [
        'faker' => [
            'locale' => 'fr_FR',
        ],
    ],
];

return [
    'when@dev' => $foundry,
    'when@test' => $foundry,
];
