# lychen/config

The Symfony configuration every Lychen API shares, so the backend services stay aligned.

`config/packages/` and `config/routes/` hold the shared base of the files of the same name
in an API. Each API keeps its own file, which imports that base and then adds what is its
own:

```php
// projects/espace/api/config/packages/api_platform.php
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfigBundle/config/packages/api_platform.php');

    $containerConfigurator->extension('api_platform', [
        'title' => 'Espace',
    ]);
};
```

The API's config comes after the import, so it has the last word: a scalar replaces the
shared value, and a plain list such as `custom_authenticators` is usually appended to.
`bin/console debug:config <extension>` shows the result.

## Wiring an API

1. `composer.json`: require `"lychen/config": "*"` and add it to the `versions`
   of the `../packages/*` path repository, then
   `composer update lychen/config`.
2. `moon.yml`: add `lychen-php-config` to `dependsOn`.
3. `config/bundles.php`: `Lychen\Config\LychenConfigBundle::class => ['all' => true]`.
   The bundle configures nothing by itself, it only resolves `@LychenConfigBundle/`.
4. `config/services.php`: set the `lychen.service` parameter to the API's domain
   (`tera`, `espace`, `flora`…). It names the RabbitMQ queue and routing keys
   (`<domain>.events`, `<domain>.internal.message.v1`) and the Mercure issuer
   (`urn:lychen:<domain>-api`).
5. For each file of `config/packages/` and `config/routes/` the library shares, make the
   API's file import its base, as above, and keep only the API's differences in it.

Then compare `bin/console debug:config <extension>` and `debug:router` before and after,
in dev, test and prod.

## What stays in the API

- **Its differences**, after the import: its OpenAPI title, its HTTP clients, an extra
  CORS method…
- **Security rules.** SecurityBundle accepts `access_control` from a single config only,
  so `security.php` declares the API's rules after the import. No firewall can be added
  after the shared `dev` and `main` either: an API adds its authenticators to
  `firewalls.main.custom_authenticators` and its providers to
  `providers.all_users.chain.providers`, which are appended after the Zitadel ones.
- **Its domain.** The `util_zitadel` user class, workflows, services.

## Writing a shared file

- Keep the APIs' style: a closure configuring the container, with
  `$containerConfigurator->env()` for what only applies to one environment. Never
  configure an extension outside the environments that register it (`web_profiler`,
  `debug`, `zenstruck_foundry` and `dama_doctrine_test` in dev/test, `sentry` in prod).
- Only what every API wants belongs here; the rest stays in the APIs' files.

## Test environment

`security.php` makes `security` a public alias of `Security`, and `mercure.php` decorates
the default Mercure hub with `Test\MercureHubStub` so tests never reach the network: the
stub publishes nothing, and takes everything else from the hub as configured.

Locally, changes to this library reach an API only after
`composer reinstall lychen/config` in it: the path repository mirrors the files
rather than symlinking them, and `composer update` skips the package as long as its
`composer.json` is unchanged.
