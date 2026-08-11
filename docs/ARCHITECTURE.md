# Architecture

## Shape

RISHIT is a modular Laravel 13 monolith running in one application deployment with MySQL and Redis. Public web pages use Blade SSR; Alpine adds only small interaction. Future API clients use `/api/v1` and share application/domain rules with web controllers.

```text
Blade controllers ─┐
                   ├─ application/domain logic ─ data + provider adapters
API controllers ───┘
```

## Implemented foundation

- `app/Domain/Shared`: `Currency` and immutable integer-minor-unit `Money`
- `app/Http/Middleware/SetLocale`: URL-controlled `sq`/`en` locale
- `app/Http/Controllers/Auth`: minimal native web session auth
- `app/Models/User`: one buyer/seller identity with Sanctum token support
- `resources/views`: server-rendered original design and auth pages
- `routes/api.php`: versioned health and read-only listing resources
- `config/marketplace.php`: stable `sq`/`en` locales, EUR-only marketplace currency, and initial auction timing defaults

## Implemented catalog

- `app/Domain/Catalog`: listing condition/status values and shared filter validation
- `app/Models`: categories, brands, unique-item listings, ordered images, and a favorites pivot
- `Listing::filter()`: one filter/query implementation reused by Blade and `/api/v1`
- Listing policy: owners alone can edit, hide, republish, or soft-delete their listings
- Laravel public storage: hashed upload names with validated image type, size, and count
- Blade SSR: home feed, catalog, listing detail, seller profile, favorites, and listing management

## Planned domain boundaries

- **Catalog:** implemented; moderation and richer category metadata remain later work
- **Commerce:** orders, item reservation, fees, state transitions, snapshots
- **Payments:** internal payment states/events plus a POK adapter
- **Shipping:** internal shipment states/events plus a selected courier adapter
- **Auctions:** auctions, bids, locking, increments, anti-sniping, closure
- **Trust:** reviews, reports, disputes, moderation/audit

These boundaries are documented, not empty code scaffolds. Provider contracts will be written with the first real use case/adapter so they reflect actual capabilities.

## Data and infrastructure

- MySQL 8.4 is authoritative for transactional state and row locking.
- Redis backs cache and queues; the database remains authoritative if realtime delivery is missed.
- Laravel Storage will abstract local development and S3-compatible production objects.
- Queued external operations must be retry-safe and idempotent.
- Horizon and Reverb are deferred until corresponding production workloads exist.

## Current database

Foundation tables are joined by `categories`, `brands`, `listings`, `listing_images`, and `favorites`. Listings are soft-deleted, carry explicit condition/status/currency values, and represent one unique physical item. New and publicly visible listings are EUR; legacy non-EUR rows remain preserved and private. Commerce tables do not exist yet.

## Decision log

- Laravel 13 / PHP 8.5: current supported stable foundation.
- Sail/Docker: isolates the project from outdated host PHP/MySQL/Redis.
- Blade-first: required for crawlable marketplace content.
- Native auth over a full starter kit: enough for the foundation without Livewire/Flux UI.
- No provider interfaces yet: avoids freezing guessed POK/courier semantics.
