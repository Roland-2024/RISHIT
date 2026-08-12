# MVP

This file describes product phases. [ROADMAP.md](ROADMAP.md) is authoritative for numbered task scope, dependencies, readiness, and blockers.

## Goal

Complete one reliable fixed-price marketplace loop before expanding feature breadth:

seller lists → buyer discovers → backend locks item → buyer pays → seller ships → delivery confirms → dispute window closes → settlement eligibility is recorded.

## Delivery phases

1. **Foundation — implemented:** Laravel/Blade, Docker, MySQL, Redis, Tailwind/Alpine, localization, session auth, Sanctum readiness, money presentation, docs, and `/api/v1` convention.
2. **Catalog — implemented:** categories, brands, seller profiles, listings/images, favorites, search/filtering, owner visibility controls, SSR pages, and read-only API resources.
3. **Commerce core:** provider-neutral order snapshots, zero-fee policy, and normalized transitions are implemented; payment abstraction, POK adapter, provider events, idempotency, and financial audit records remain later work.
4. **Shipping:** selected courier adapter, quotes/snapshots, shipment lifecycle, tracking events, returns foundation.
5. **Fixed-price transaction:** production-quality Buy Now through delivery and completion.
6. **Offers:** accepted offers reuse the fixed-price commerce path.
7. **Auctions:** concurrency-safe bids, anti-sniping, realtime enhancement, closing, winner checkout, and failure handling.
8. **Trust:** transaction-linked reviews, reports, disputes, moderation, verification.
9. **SEO hardening:** product/category/brand sitemaps, structured data, canonical/facet strategy, hreflang, crawler validation.
10. **Flutter-ready API:** complete mobile API and Sanctum flows only after web commerce is stable.

## Explicitly outside Phase 1

Production checkout/payment/courier integrations, dynamic fees, escrow claims, admin interfaces, Livewire, Horizon, Reverb, advanced search, microservices, analytics platforms, and Flutter.

Future reservation, cancellation, late-payment, and seller-handoff behavior is
defined in [FIXED-PRICE-POLICY.md](FIXED-PRICE-POLICY.md). The record does not
enable any payment or courier method.

## MVP success

Correctness, traceability, trust, and completion rate matter more than feature count. No next feature outranks a broken payment/shipping/order loop.
