# MVP

This file describes product phases. [ROADMAP.md](ROADMAP.md) is authoritative for numbered task scope, dependencies, readiness, and blockers.

## Goal

Complete one reliable fixed-price marketplace loop before expanding feature breadth:

seller lists → buyer discovers → backend locks item → buyer pays → seller ships → delivery confirms → dispute window closes → settlement eligibility is recorded.

## Delivery phases

1. **Foundation — implemented:** Laravel/Blade, Docker, MySQL, Redis, Tailwind/Alpine, localization, session auth, Sanctum readiness, money presentation, docs, and `/api/v1` convention.
2. **Catalog — implemented:** categories, brands, seller profiles, listings/images, favorites, search/filtering, owner visibility controls, SSR pages, and read-only API resources.
3. **Commerce core:** immutable money snapshots, fees, orders, payment abstraction, POK adapter, provider events, idempotency, and financial audit records.
4. **Shipping:** selected courier adapter, quotes/snapshots, shipment lifecycle, tracking events, returns foundation.
5. **Fixed-price transaction:** production-quality Buy Now through delivery and completion.
6. **Offers:** accepted offers reuse the fixed-price commerce path.
7. **Auctions:** concurrency-safe bids, anti-sniping, realtime enhancement, closing, winner checkout, and failure handling.
8. **Trust:** transaction-linked reviews, reports, disputes, moderation, verification.
9. **SEO hardening:** product/category/brand sitemaps, structured data, canonical/facet strategy, hreflang, crawler validation.
10. **Flutter-ready API:** complete mobile API and Sanctum flows only after web commerce is stable.

## Explicitly outside Phase 1

Production payment/courier integrations, marketplace tables, dynamic fees, escrow claims, admin frameworks, Livewire, Horizon, Reverb, advanced search, microservices, analytics platforms, and Flutter.

## MVP success

Correctness, traceability, trust, and completion rate matter more than feature count. No next feature outranks a broken payment/shipping/order loop.
