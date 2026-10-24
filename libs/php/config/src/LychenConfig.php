<?php

declare(strict_types=1);

namespace Lychen\Config;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The Symfony configuration every Lychen API shares, so the backend services stay aligned.
 *
 * The bundle configures nothing by itself: it lets an API's config files import their
 * shared base from this library, as `@LychenConfig/config/packages/<file>.php` and
 * `@LychenConfig/config/routes/<file>.php`, before adding what is their own.
 */
final class LychenConfig extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
