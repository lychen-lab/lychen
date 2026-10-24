# lychen/test

The PHPUnit base classes every Lychen API's functional and security tests build on.

- `Lychen\Test\AbstractApiTestCase`: API Platform's `ApiTestCase` with a database reset
  per test (Foundry's `#[ResetDatabase]`), Foundry factories, the Messenger test transports
  and a `browser()` that speaks JSON-LD by default. `commitAndDie()` commits the
  transaction DAMA keeps open and stops, to inspect the database of a failing test.
- `Lychen\Test\TestBrowser`: the Zenstruck `KernelBrowser` the tests drive. `actingAs()`
  reloads the managed entity so the authenticated user takes part in Doctrine's identity
  map, `patch()` sends a JSON merge patch and `addMock()` replaces a service of the test
  container.

## Wiring an API

1. `composer.json`: require `"lychen/test": "*"` in `require-dev` and add it to the
   `versions` of the `../packages/*` path repository, then `composer update lychen/test`.
2. `moon.yml`: add `lychen-php-test` to `dependsOn`.
3. `.env.test`: `KERNEL_BROWSER_CLASS=Lychen\Test\TestBrowser` (Zenstruck reads it to
   instantiate the browser; `browser()` refuses any other class).
4. `phpunit.xml.dist`: register DAMA's, Zenstruck Browser's and Foundry's extensions
   (`DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension`,
   `Zenstruck\Browser\Test\BrowserExtension`, `Zenstruck\Foundry\PHPUnit\FoundryExtension`).
   Foundry's boots the factories and honours the base class' `#[ResetDatabase]`; there
   are no Foundry traits to use.
5. Tests extend `Lychen\Test\AbstractApiTestCase`, or an API-level subclass of it that
   adds the API's own test traits.

Locally, changes to this library reach an API only after `composer reinstall lychen/test`
in it: the path repository mirrors the files rather than symlinking them.
