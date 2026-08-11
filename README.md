# RISHIT

RISHIT is a transactional C2C marketplace for second-hand fashion, launching in Albania and designed to expand to Kosovo. The first product is an SEO-first Laravel and Blade web application; a Flutter client is deliberately deferred until the commerce flows and API are stable.

## Current implementation

- Laravel 13.24 on the PHP 8.5 Sail runtime
- Blade SSR, Tailwind CSS 4, and Alpine.js
- MySQL 8.4 and Redis via Docker Compose
- Albanian (`/sq`) and English (`/en`) public routes
- Web session authentication and Sanctum-ready users
- EUR-only pricing stored as integer cents
- €0 listing fees and €0 seller fees
- Redis cache/queue configuration and a versioned `/api/v1/health` endpoint
- Database-backed categories, brands, listings, ordered photos, favorites, and seller profiles
- Localized catalog search/filtering and owner listing management
- Read-only `/api/v1/listings` collection and item endpoints
- PHPUnit 12 tests

Payments, shipping, orders, offers, auctions, messaging, reviews, and moderation are not implemented yet. See [docs/MVP.md](docs/MVP.md).

## Requirements

- Docker Desktop with Docker Compose v2
- Git
- Node.js 22+ only if running frontend commands on the host; the Sail container already includes Node

Local PHP, MySQL, and Redis are not required.

## Setup on Windows PowerShell

```powershell
Copy-Item .env.example .env
docker run --rm -v "${PWD}:/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install
docker compose up -d --build
docker compose exec laravel.test chmod -R a+rwX storage bootstrap/cache
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan storage:link
docker compose exec laravel.test php artisan db:seed
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run build
```

Open [http://localhost:8080](http://localhost:8080). The default `.env.example` forwards MySQL to `3307` and Redis to `6380` to reduce conflicts with host services.

On macOS/Linux, `./vendor/bin/sail` may be used instead of `docker compose exec laravel.test` after dependencies are installed.

## Common commands

```powershell
# Start or stop the stack
docker compose up -d
docker compose down

# Application and database
docker compose exec laravel.test php artisan about
docker compose exec laravel.test php artisan migrate
docker compose exec laravel.test php artisan db:seed

# Frontend
docker compose exec laravel.test npm run dev
docker compose exec laravel.test npm run build

# Queue worker (Redis)
docker compose exec laravel.test php artisan queue:work --tries=3

# Tests and formatting
docker compose exec laravel.test php artisan test
docker compose exec laravel.test vendor/bin/pint --test

# Service checks
docker compose exec mysql mysqladmin ping -ppassword
docker compose exec redis redis-cli ping
```

The deterministic development seeder creates catalog categories, brands, eight placeholder listings, and seller `ana@rishit.test` with password `password`. Never use those credentials in production. Horizon and Reverb are not installed yet; add Horizon with the first meaningful asynchronous production workload and Reverb with auctions, messaging, or another real realtime flow.

## Configuration

Copy `.env.example`, never commit `.env`, and keep credentials outside source control. Important development values:

- `DB_*`: MySQL service credentials
- `REDIS_*`: cache and queue service
- `APP_PORT`, `VITE_PORT`, `FORWARD_DB_PORT`, `FORWARD_REDIS_PORT`: host ports

Marketplace defaults that are safe to define now live in `config/marketplace.php`. Albanian and English content share one EUR-only marketplace process with no listing or seller fees. Buyer-side fees, payout timing, dispute periods, and courier pricing remain unresolved and are not hardcoded.

## Architecture

RISHIT is a modular Laravel monolith. Blade and future API controllers must call the same application/domain logic. Public indexable content remains server-rendered. Provider-specific payment and shipping data will stay at adapter boundaries once real integrations exist; no speculative interfaces or fake providers are present today.

Start with [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md), [docs/BUSINESS-RULES.md](docs/BUSINESS-RULES.md), and [AGENTS.md](AGENTS.md).

## Troubleshooting

- Port conflict: change the four forwarded ports in `.env`, then recreate containers.
- Missing Vite manifest: run `docker compose exec laravel.test npm run build`.
- Blade `tempnam()` or permission error on Windows: run `docker compose exec laravel.test chmod -R a+rwX storage bootstrap/cache`, then `docker compose exec -u sail laravel.test php artisan view:clear`.
- Database not ready: wait for `docker compose ps` to report MySQL healthy, then rerun migrations.
- Stale configuration: run `docker compose exec laravel.test php artisan optimize:clear`.
- Clean dependency bootstrap on Windows: use the documented Docker Composer command; the host PHP version is irrelevant.
