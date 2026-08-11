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
- `app/Models/Address`: private, owner-scoped Albania address-book records; country is fixed to `AL`
- `app/Http/Requests`: reusable profile and address validation for Blade now and authenticated `/api/v1` controllers later
- `resources/views`: server-rendered original design and auth pages
- `routes/api.php`: versioned health and read-only listing resources
- `config/marketplace.php`: stable `sq`/`en` locales, EUR-only marketplace currency, and initial auction timing defaults

## Implemented catalog

- `app/Domain/Catalog`: listing condition/status values and shared filter validation
- `app/Models`: categories, brands, unique-item listings, ordered images, and a favorites pivot
- `Listing::filter()`: one filter/query implementation reused by Blade and `/api/v1`
- `Listing::isPubliclyVisible()`: one availability decision reused by public detail and favorite endpoints
- Listing policy: owners alone can edit, hide, republish, or soft-delete their listings
- Laravel public storage: hashed upload names with validated image type, size, and count
- Blade SSR: home feed, catalog, listing detail, seller profile, favorites, and listing management
- Blade account settings: profile edits and an owner-authorized private address book in `sq` and `en`

## Implemented commerce foundation

- Provider-neutral orders with immutable item, party, address, EUR amount, and fee-policy snapshots
- Shared application actions for transaction-safe order creation and explicit normalized state transitions
- Buyer, seller, and administrative order access through Laravel policy discovery
- Append-only transition history; no checkout, provider calls, payment records, shipment workflow, accounting ledger, or seller-settlement claim

## Planned domain boundaries

- **Catalog:** implemented; moderation and richer category metadata remain later work
- **Commerce:** foundational orders, item reservation, fees, state transitions, and snapshots are implemented; checkout and exceptional-policy workflows remain later work
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

Foundation tables are joined by `categories`, `brands`, `listings`, `listing_images`, `favorites`, `orders`, and `order_transitions`. Listings are soft-deleted, carry explicit condition/status/currency values, and represent one unique physical item. New and publicly visible listings are positive-price EUR inventory; legacy non-EUR rows remain preserved and private. Composite indexes support current public and participant order queries.

## Decision log

- Laravel 13 / PHP 8.5: current supported stable foundation.
- Sail/Docker: isolates the project from outdated host PHP/MySQL/Redis.
- Blade-first: required for crawlable marketplace content.
- Native auth over a full starter kit: enough for the foundation without Livewire/Flux UI.
- No provider interfaces yet: avoids freezing guessed POK/courier semantics.
