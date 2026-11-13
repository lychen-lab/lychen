# Load test

Several instances of the API's production image behind a load balancer, on one database, and
[k6](https://grafana.com/docs/k6/) to send them requests along a curve.

```
k6 ──> lb (Caddy, round robin) ──> api × 3 (production image) ──> database (Postgres), redis
```

It is a stack of its own (`compose.load.yml`, project `flora-api-load`): it never touches the
dev stack and may run beside it.

## Run it

```bash
moon run flora-api:load-up      # build, migrate, fill the database, start the instances
moon run flora-api:load-test    # send the requests (default curve: ramp)
moon run flora-api:load-down    # stop and delete everything, data included
```

During a run, the curves are live on <http://localhost:5665>; at its end, the report is kept in
`load/reports/<profile>.html`. The API itself is on <http://localhost:8480>.

## Curves

`PROFILE` picks the curve of virtual users, `VUS` its highest number of users (50), `DURATION`
its length in seconds (120):

```bash
PROFILE=spike VUS=200 DURATION=180 moon run flora-api:load-test
```

| Profile  | Curve                                                    |
| -------- | -------------------------------------------------------- |
| `smoke`  | One user: does the scenario work?                        |
| `fixed`  | The same load from start to end                          |
| `ramp`   | A slow climb, then a plateau                             |
| `spike`  | A low base load, and a sudden burst in the middle        |
| `peak`   | Up to a peak, held, and back down                        |
| `stress` | Steps, each higher than the last: where does it give way |

A run fails when more than 1 % of the requests fail or when their 95th percentile is over
800 ms (`thresholds` in `script.js`).

## Instances

```bash
API_REPLICAS=2 moon run flora-api:load-up                # 2 instances instead of 3
API_CPUS=2 API_MEMORY=2G moon run flora-api:load-up      # what each instance gets (1 CPU, 1G)
```

Every instance gets the same share of the machine, so the same curve on 1, 2 then 3 instances
shows what an instance adds. Which instance answered a request is in its `X-Upstream` header.

## Data

`load-up` fills the database once with `src/Story/LoadTestStory.php`: the reference data, 2 000
plants, 200 seed libraries and 20 000 seed lots. Change its constants for another volume, then
`load-down` and `load-up`.

## Files

| File                             | Role                                                    |
| -------------------------------- | ------------------------------------------------------- |
| `compose.load.yml`               | The stack: database, redis, seed, api × N, lb, k6       |
| `load/Caddyfile`                 | The load balancer                                       |
| `load/script.js`                 | The curves, and what a user does (`requests`)           |
| `src/Story/LoadTestStory.php`    | The data                                                |
| `moon.yml`                       | The `load-up`, `load-test` and `load-down` tasks        |

To load-test another API, copy these and change the scenario's `requests` and the story.
