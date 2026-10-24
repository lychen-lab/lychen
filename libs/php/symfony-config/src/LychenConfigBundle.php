<?php

declare(strict_types=1);

namespace Lychen\ConfigBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The Symfony configuration every Lychen API shares, so the backend services stay aligned.
 *
 * The bundle configures nothing by itself: it lets an API's config files import their
 * shared base from this library, as `@LychenConfigBundle/config/packages/<file>.php` and
 * `@LychenConfigBundle/config/routes/<file>.php`, before adding what is their own.
 */
final class LychenConfigBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
