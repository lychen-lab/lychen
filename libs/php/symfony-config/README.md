# lychen/symfony-config

The Symfony configuration every Lychen API shares, so the backend services stay aligned.

`LychenConfigBundle` prepends the presets of `config/packages/` — one per extension —
before the API's own configuration. The API only declares what is its own, and any file
of its `config/packages/` overrides a preset key: the app always has the last word.

## Wiring an API

1. `composer.json`: require `"lychen/symfony-config": "*"` and add it to the `versions`
   of the `../packages/*` path repository, then
   `composer update lychen/symfony-config`.
2. `moon.yml`: add `lychen-php-symfony-config` to `dependsOn`.
3. `config/bundles.php`: `Lychen\ConfigBundle\LychenConfigBundle::class => ['all' => true]`.
4. `config/packages/lychen_config.php`: `'service' => '<domain>'` — it names the RabbitMQ
   queue and routing keys (`<domain>.events`, `<domain>.internal.message.v1`) and the
   OpenAPI title.
5. `config/routes.php`: `$routes->import('@LychenConfigBundle/config/routes.php');`
   before the API's own controllers.
6. `config/packages/security.php`: only the API's `access_control`, plus its own
   authenticators and providers if any (see below).

Then delete the API's copies of what the presets cover, and compare
`bin/console debug:config <extension>` and `debug:router` before and after, in dev, test
and prod.

## What stays in the API

- **Security rules.** SecurityBundle accepts `access_control` from a single config only,
  and no new firewall can be added after the preset's `dev` and `main`. An API adds its
  authenticators to `firewalls.main.custom_authenticators` and its providers to
  `providers.all_users.chain.providers`: both are lists, appended after the Zitadel ones.
- **Its domain.** The `util_zitadel` user class, workflows, HTTP clients, services.

## Writing a preset

- Return an **array**, never a closure. In `prependExtension()` an array file is
  prepended; a closure calling `$containerConfigurator->extension()` is appended and
  would silently override the API.
- Register it in `LychenConfigBundle::PRESETS` with the extensions it configures: it is
  only imported when they are all registered for the current environment.
- `$env` holds the kernel environment, and `when@<env>` keys work as usual.

## Test environment

The bundle makes `security` a public alias of `Security`, and stubs the default Mercure
hub (`Test\MercureHubStub`) so tests never reach the network.

Locally, changes to this library reach an API only after
`composer reinstall lychen/symfony-config` in it: the path repository mirrors the files
rather than symlinking them, and `composer update` skips the package as long as its
`composer.json` is unchanged.
