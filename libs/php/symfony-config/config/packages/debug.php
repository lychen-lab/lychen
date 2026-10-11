<?php

declare(strict_types=1);

return [
    'when@dev' => [
        'debug' => [
            'dump_destination' => 'tcp://%env(VAR_DUMPER_SERVER)%',
        ],
    ],
];
