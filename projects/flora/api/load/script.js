// The load test's scenario, run by k6 (see README.md).
//
//   -e PROFILE=smoke|fixed|ramp|spike|peak|stress   the curve of virtual users
//   -e VUS=50                                       its highest number of virtual users
//   -e DURATION=120                                 its length, in seconds
//
// To use it for another API: change `requests` (what a user does) and `setup`.
import { check, sleep } from 'k6';
import http from 'k6/http';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8480';
const PROFILE = __ENV.PROFILE || 'ramp';
const VUS = Number(__ENV.VUS || 50);
const DURATION = Number(__ENV.DURATION || 120);

// Each curve is a list of [share of the duration, share of VUS to reach].
const PROFILES = {
  // One user, to check that the scenario works.
  smoke: [[1, 1 / VUS]],
  // The same load from start to end.
  fixed: [[0.05, 1], [0.95, 1]],
  // A slow climb, then a plateau.
  ramp: [[0.5, 1], [0.4, 1], [0.1, 0]],
  // A low base load, and a sudden burst in the middle.
  spike: [[0.4, 0.2], [0.05, 1], [0.15, 1], [0.05, 0.2], [0.35, 0.2]],
  // Up to a peak, held, and back down.
  peak: [[0.2, 0.2], [0.2, 1], [0.2, 1], [0.2, 0.2], [0.2, 0.2]],
  // Steps, each higher than the last, to find where the API gives way.
  stress: [[0.05, 0.25], [0.2, 0.25], [0.05, 0.5], [0.2, 0.5], [0.05, 0.75], [0.2, 0.75], [0.05, 1], [0.2, 1]],
};

if (!PROFILES[PROFILE]) {
  throw new Error(`Unknown PROFILE "${PROFILE}": ${Object.keys(PROFILES).join(', ')}`);
}

export const options = {
  scenarios: {
    [PROFILE]: {
      executor: 'ramping-vus',
      startVUs: 1,
      stages: PROFILES[PROFILE].map(([time, vus]) => ({
        duration: `${Math.max(1, Math.round(time * DURATION))}s`,
        target: Math.max(1, Math.round(vus * VUS)),
      })),
      gracefulRampDown: '5s',
    },
  },
  // The run fails (exit code 99) when the API does not hold these.
  thresholds: {
    http_req_failed: ['rate<0.01'],
    http_req_duration: ['p(95)<800'],
  },
};

const JSON_LD = { 'Content-Type': 'application/ld+json' };
const PER_PAGE = 30;

function get(name, path) {
  // `name` groups the URLs of one endpoint in the results, whatever their id or page.
  const response = http.get(`${BASE_URL}${path}`, { tags: { name } });
  check(response, { [`${name} is 200`]: (r) => r.status === 200 });
  return response;
}

function randomPage(total) {
  return 1 + Math.floor(Math.random() * Math.max(1, Math.ceil(total / PER_PAGE)));
}

function pick(list) {
  return list[Math.floor(Math.random() * list.length)];
}

// What a user does, and how often compared to the rest (weight).
const requests = [
  {
    weight: 35,
    run: (data) => get('GET /api/seed_lots', `/api/seed_lots?page=${randomPage(data.seedLots)}`),
  },
  {
    weight: 20,
    run: (data) => {
      const page = get('GET /api/seed_lots', `/api/seed_lots?page=${randomPage(data.seedLots)}`);
      const lot = pick(page.json('member') || []);
      if (lot) get('GET /api/seed_lots/{uuid}', `/api/seed_lots/${lot.uuid}`);
    },
  },
  {
    weight: 15,
    run: (data) => get('GET /api/plants', `/api/plants?page=${randomPage(data.plants)}`),
  },
  {
    weight: 15,
    run: (data) =>
      get('GET /api/seed_libraries', `/api/seed_libraries?page=${randomPage(data.seedLibraries)}`),
  },
  {
    weight: 10,
    run: () => get('GET /api/characteristics', '/api/characteristics'),
  },
  {
    // A write: a new lot, then its reservation.
    weight: 5,
    run: (data) => {
      const created = http.post(
        `${BASE_URL}/api/seed_lots`,
        JSON.stringify({
          variety: `Load test ${__VU}-${__ITER}`,
          harvestYear: 2026,
          quantityInGrams: 50,
          seedLibrary: pick(data.libraryUuids),
        }),
        { headers: JSON_LD, tags: { name: 'POST /api/seed_lots' } },
      );
      if (!check(created, { 'POST /api/seed_lots is 201': (r) => r.status === 201 })) return;
      const reserved = http.patch(`${BASE_URL}/api/seed_lots/${created.json('uuid')}/reserve`, null, {
        headers: { 'Content-Type': 'application/merge-patch+json' },
        tags: { name: 'PATCH /api/seed_lots/{uuid}/reserve' },
      });
      check(reserved, { 'PATCH reserve is 200': (r) => r.status === 200 });
    },
  },
];

const totalWeight = requests.reduce((sum, request) => sum + request.weight, 0);

// Once, before the run: what the scenario needs to know about the data.
export function setup() {
  const total = (path) => http.get(`${BASE_URL}${path}`).json('totalItems');
  const libraries = http.get(`${BASE_URL}/api/seed_libraries`).json();
  if (!libraries.totalItems) {
    throw new Error('The database is empty: run `moon run flora-api:load-up` first.');
  }
  return {
    seedLots: total('/api/seed_lots'),
    plants: total('/api/plants'),
    seedLibraries: libraries.totalItems,
    libraryUuids: libraries.member.map((library) => library.uuid),
  };
}

// One iteration of one virtual user: a request picked by weight, then a pause.
export default function (data) {
  let roll = Math.random() * totalWeight;
  const request = requests.find((candidate) => (roll -= candidate.weight) < 0);
  request.run(data);
  sleep(0.5 + Math.random());
}
