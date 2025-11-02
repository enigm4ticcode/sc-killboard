# Star Citizen Killboard

A Laravel 12 application that parses Star Citizen `Game.log` files and publishes a public killboard with player and organization leaderboards. Built with Livewire 3, Tailwind CSS 4, Scout (Meilisearch), Redis, and Laravel Sail for local development.


## Contents
- Overview
- Tech Stack
- Prerequisites
- Quick Start (Laravel Sail)
- Environment Configuration
- Running the App (HTTP, Vite, Queues)
- Database & Search (migrate/seed, Scout/Meilisearch)
- Testing (Pest)
- Useful Commands
- Troubleshooting


## Overview
Main HTTP routes (see `routes/web.php`):
- `/` — Home/kill feed
- `/players/{name}` — Player profile page
- `/organizations/{name}` — Organization profile page
- `/how-it-works` — App details for users
- `/api-documentation` — Basic API docs
- `/legal` — Legal page
- Authenticated:
  - `/profile` — Profile settings
  - `/services/verification` — RSI verification flow
  - `/services/upload-log` — Upload `Game.log` files


## Tech Stack
- PHP 8.4 (via Sail container)
- Laravel 12
- Livewire 3
- Tailwind CSS 4 + Vite
- MySQL 8, Redis, Meilisearch (all via Sail)
- Pest 4 (tests)

Services are defined in `compose.yaml` and include:
- `laravel.test` (PHP-FPM + Nginx + app)
- `mysql` (8.0)
- `redis` (alpine)
- `meilisearch` (latest)


## Prerequisites
- Docker Desktop (or Docker Engine) + Docker Compose
- Node.js 20+ and npm (for running Vite on the host machine)

You do NOT need to install PHP or MySQL locally — Sail runs those in containers.


## Quick Start (Laravel Sail)
1) Clone the repository and enter the folder:
```
git clone <your-fork-or-repo-url>
cd sc-killboard
```

2) Copy environment file and set an app key:
```
cp .env.example .env
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
```
If you don’t have PHP on your host, you can do this after Sail is up with `./vendor/bin/sail` (see below).

3) Boot the containers:
```
./vendor/bin/sail up -d
```
The first run will build the PHP runtime image. Subsequent runs are much faster.

4) Install JS dependencies and start Vite (on your host machine):
```
npm install
npm run dev
```
This serves assets on `http://localhost:5173` and hot-reloads the UI. The port is exposed through Sail (`compose.yaml` maps `VITE_PORT`).

5) Run database migrations:
```
./vendor/bin/sail artisan migrate
```

6) Visit the app:
- App: http://localhost
- Meilisearch: http://localhost:7700 (API)
- MySQL: forwarded on localhost:3306
- Redis: forwarded on localhost:6379

Note: The default `APP_URL` in `.env.example` is `http://laravel.test`. For Sail you can use `http://localhost` (recommended) unless you have a local DNS mapping for `laravel.test`.


## Environment Configuration
The important environment variables from `.env.example`:
- Application: `APP_NAME`, `APP_ENV`, `APP_DEBUG`, `APP_URL`
- Database (Sail): `DB_CONNECTION=mysql`, `DB_HOST=mysql`, `DB_PORT=3306`, `DB_DATABASE=laravel`, `DB_USERNAME=laravel`, `DB_PASSWORD=password`
- Cache/Session/Queue: `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`, `REDIS_HOST=redis`, `REDIS_PORT=6379`
- Scout: `SCOUT_DRIVER=meilisearch`, `MEILISEARCH_HOST=http://meilisearch:7700`, `MEILISEARCH_KEY=masterKey`
- Mail: Defaults point to `mailpit`, but a Mailpit container is not included by default. You can change `MAIL_*` to your provider, run Mailpit locally, or add a Mailpit service to `compose.yaml`.
- App-specific tuning: `MOST_RECENT_KILLS_DAYS`, `LEADERBOARDS_TIMESPAN_DAYS`, `PAGINATION_KILLS_PER_PAGE`, `LEADERBOARDS_NUMBER_OF_TOP_POSITIONS`

When using Sail, keep `DB_HOST=mysql`, `REDIS_HOST=redis`, and `MEILISEARCH_HOST=http://meilisearch:7700` because those are the container service names.


## Running the App
- Start/stop containers:
```
./vendor/bin/sail up -d
./vendor/bin/sail down
```

- Artisan commands inside Sail:
```
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan queue:listen --tries=1
```

- Queues: This project offloads certain work to Redis-backed queues. Run a worker while developing:
```
./vendor/bin/sail artisan queue:listen --tries=1
```

- Vite dev server (recommended on host):
```
npm run dev
```
Alternatively, if you prefer running Vite inside the container and your Sail image includes Node, you can try:
```
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```
If HMR doesn’t load, keep Vite on the host and ensure port 5173 is free.


## Database & Search
- Run migrations:
```
./vendor/bin/sail artisan migrate
```

- Optional seeders (example creates a test user):
```
./vendor/bin/sail artisan db:seed
```
See `database/seeders/DatabaseSeeder.php` for details.

- Meilisearch (Scout):
  - Meilisearch is started by Sail and exposed at `http://localhost:7700`.
  - API key: `MEILISEARCH_KEY=masterKey` (from `.env.example`, change in production).
  - After changing indices or searchable data, you may need to re-import/searchable data depending on your models.


## Testing (Pest)
This project includes a comprehensive Pest v4 test suite covering core services and behaviors. Highlights:
- Services: GameLogService, LeaderboardService, RsiAccountVerificationService, RsiStatusService, StarCitizenWikiService, and VehicleService.
- HTTP calls are faked via `Http::fake()` so tests run fully offline.
- Database tests run against an isolated testing database as configured in `phpunit.xml`.

Run the full test suite inside Sail:
```
./vendor/bin/sail test
```
Or run Pest directly on your host:
```
composer test
```

Common examples:
- Run a single test file:
```
./vendor/bin/sail artisan test tests/Unit/GameLogServiceTest.php
```
- Filter by test class or name:
```
./vendor/bin/sail artisan test --filter=LeaderboardServiceTest
./vendor/bin/sail artisan test --filter="recordKill creates models"
```

Notes:
- The `phpunit.xml` config sets a MySQL testing database (`DB_CONNECTION=mysql`, `DB_DATABASE=testing`). Sail’s MySQL container auto-creates a `testing` database for integration tests via an init script.
- If you don’t use Sail, ensure a local MySQL database named `testing` exists (or override via env vars when running tests).


## Useful Commands
- Bring the app up/down:
```
./vendor/bin/sail up -d
./vendor/bin/sail down
```
- Rebuild the app container after changing PHP extensions or dependencies:
```
./vendor/bin/sail build --no-cache
```
- Tail logs for a service:
```
./vendor/bin/sail logs -f laravel.test
./vendor/bin/sail logs -f mysql
./vendor/bin/sail logs -f redis
./vendor/bin/sail logs -f meilisearch
```
- Run a one-off bash shell in the app container:
```
./vendor/bin/sail bash
```


## Troubleshooting
- Vite/HMR not loading:
  - Run `npm run dev` on the host (preferred) and ensure port 5173 is available.
  - If you must run Vite in the container, verify `compose.yaml` exposes the Vite port and consider setting `VITE_PORT=5173` in `.env`.

- "Unable to locate file in Vite manifest" error in the browser:
  - Build assets: `npm run build` (host) or `./vendor/bin/sail npm run build` (container), then reload.

- Database connection issues:
  - Ensure `DB_HOST=mysql` in `.env`.
  - `./vendor/bin/sail ps` and `./vendor/bin/sail logs -f mysql` for health.

- Meilisearch unavailable:
  - Confirm the container is healthy: `./vendor/bin/sail ps` and `./vendor/bin/sail logs -f meilisearch`.
  - Ensure `MEILISEARCH_HOST` points to `http://meilisearch:7700` inside the app and `http://localhost:7700` from your browser.

- Queues not processing:
  - Start a worker: `./vendor/bin/sail artisan queue:listen`.


## Local (non-Sail) development (optional)
If you prefer to run PHP/MySQL/Redis locally, the repository includes a convenience script:
```
composer run dev
```
It uses `concurrently` to run multiple processes (Laravel dev server, queue listener, Pail logs, and Vite) on your host. You’ll still need a working MySQL/Redis locally and proper `.env` values.


## License
MIT — see `LICENSE` if present in the repository.
