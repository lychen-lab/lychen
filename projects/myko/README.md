# Myko

Mushroom cultivation (mushroom logs, their inoculation, journal and harvests). Read
[`CLAUDE.md`](CLAUDE.md) and [`docs/myko/spec.fr.md`](../../docs/myko/spec.fr.md) first.

## API (`projects/myko/api`, `myko-api`)

```bash
moon myko-api:dev             # http://localhost:8082, PostgreSQL on localhost:23432
moon myko-api:lint            # Doctrine schema validation, then PHPStan
moon myko-api:format          # php-cs-fixer (root config)
moon myko-api:test            # whole PHPUnit suite
```

### Environment variables

`.env` only names them: set the values in `.env.local` (dev), the compose environment, or the
deployment. None of them is needed by the tests (`.env.test`, `actingAs()`).

| Variable                                     | What for                                                                                                                                                                             |
| -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `APP_ENV`, `APP_SECRET`                      | Symfony environment and secret.                                                                                                                                                      |
| `DATABASE_URL`                               | PostgreSQL. Set by `compose.yml` for the local stack.                                                                                                                                |
| `REDIS_URL`                                  | Cache. Set by `compose.yml`.                                                                                                                                                         |
| `MESSENGER_TRANSPORT_DSN`                    | Central RabbitMQ (`projects/common/rabbitmq`), as the `myko` user: queue `myko.events`, routing keys `myko.*`. Defaults to `amqp://myko:myko@rabbitmq:5672/lychen` in `compose.yml`. |
| `CORS_ALLOW_ORIGIN`                          | Origins allowed to call the API (the Myko app).                                                                                                                                      |
| `MAILER_DSN`                                 | Outgoing mail (mailpit locally).                                                                                                                                                     |
| `SENTRY_DSN`                                 | Error reporting, production only.                                                                                                                                                    |
| `ZITADEL_DOMAIN`                             | Zitadel instance that issues the users' tokens.                                                                                                                                      |
| `ZITADEL_CLIENT_ID`, `ZITADEL_CLIENT_SECRET` | The API's Zitadel application (token introspection).                                                                                                                                 |
| `ZITADEL_MTOM_PAT`                           | Personal access token of the machine user that reads Zitadel users.                                                                                                                  |
| `ZITADEL_PROJECT_ID`                         | Zitadel project of Myko (declared, not read yet).                                                                                                                                    |
| `JWT_SECRET`, `JWT_ALGORITHM`                | Declared as in Flora, not read yet (Mercure, later).                                                                                                                                 |

Myko is not deployed yet (M33): no `dokploy` tag, no Dokploy ids.
