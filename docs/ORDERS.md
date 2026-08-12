# Orders

The provider-neutral order foundation records authoritative commercial snapshots, fixed-price reservations, and normalized lifecycle transitions. It does not expose checkout or call payment or shipping providers.

## Implemented normal lifecycle

`CREATED → AWAITING_PAYMENT → PAID → AWAITING_SHIPMENT → SHIPPED → DELIVERED → COMPLETED`

The forward lifecycle remains provider-neutral. Task 13 also implements terminal pre-payment `cancelled` and `expired` outcomes from `created` or `awaiting_payment`. Dispute, refund, return, post-payment cancellation, and administrative workflow remain deferred. Invalid transitions throw rather than silently rewriting state, and the generic transition action cannot bypass reservation release or let a participant confirm payment.

Reservation behavior follows the [Fixed-Price Policy Decision Record](FIXED-PRICE-POLICY.md). Only the 15-minute instant-online profile is implemented; that internal profile does not enable a provider or checkout.

## Creation transaction

1. Lock the listing.
2. Re-check seller, buyer, listing availability, and authoritative price.
3. Calculate and snapshot the EUR item amount, fees, shipping, total, seller payable, and currency using integer cents.
4. Create the order with `fixed_price_v1_online`, a committed start, a fixed 15-minute deadline, and an active inventory claim.
5. Mark the listing `reserved` and append the initial audit transition atomically.
6. Commit without initiating external payment work.

Creation requires a non-empty buyer-scoped idempotency key. An exact retry inside the live reservation returns the original order without extending its deadline. Reusing the key with different inputs, or after release/deadline, fails and never creates a replacement order.

The listing row is locked and authoritative visibility is re-read. A nullable `inventory_claim` participates in a unique `(listing_id, inventory_claim)` constraint: one row may hold `true`, while any number of released historical rows hold `NULL`. Public catalog queries also exclude an active claim, so a concurrent or stale listing-status write cannot silently resell the item.

## Snapshots and audit

Orders retain the commercial facts needed after a listing/user changes: item title/description/condition/price, buyer/seller identities, buyer/seller addresses, fee policy/version, shipping price, and timestamps. Order amounts come from shared server-side calculation, never client totals. Payment and ledger records remain deferred until a real provider-backed use case exists; financial truth is never inferred only from `orders.state`.

Buyer fee policy `buyer_fee_none_v1` snapshots a EUR 0 fee, EUR 0 tax, EUR 0 display amount, and EUR 0 refundable amount. Seller listing and selling fees are also fixed at EUR 0. A future buyer policy requires a new approved version and cannot alter historical snapshots.

## Implemented reservation policy

- Instant online payment reserves for 15 minutes from the committed local
  reservation. Provider-session recovery and payment retries do not extend it.
- The 24-hour platform-account bank-transfer and no-timeout COD profiles remain disabled and unimplemented. Direct buyer-to-seller transfer is prohibited.
- An idempotent scheduler checks expirations every minute under a row lock. The
  next run retries failed cleanup, and three consecutive failed runs alert
  operations.
- Before verified payment, the buyer may cancel their reservation, the system may cancel a definitive payment/setup failure, and an admin may cancel with a reason and explicit `active`/`hidden` listing outcome. An unavailable-item reason must stay hidden. The seller cannot cancel directly.
- `expired` and pre-payment `cancelled` attempts remain for audit. An eligible
  listing may reactivate after buyer/system expiry or cancellation; an item
  reported unavailable becomes `hidden`.
- Late payment confirmation never revives the order or takes inventory from a
  later buyer. It enters reconciliation.

`orders:expire-reservations` scans due pre-payment claims and re-locks each order and listing before release. It is scheduled every minute. Repeated cleanup is a no-op; a failed per-order transaction leaves both the claim and listing reserved for the next run. The consecutive-failure counter resets after success and logs a critical operational alert on the third failed run.

Every release appends actor, reason, terminal state, timestamp, and resulting listing status. Reactivation occurs only when the listing is still `reserved`, EUR, positive-priced, and not deleted; a seller-hidden/deleted or otherwise ineligible item is not republished.

## Late authoritative payment evidence

A browser return, client callback, screenshot, or participant assertion cannot confirm payment. A later verified provider event/retrieval action must lock the order and inspect its inventory claim. If the reservation was already released, it must leave the terminal order and current listing untouched and record a recoverable reconciliation exception in the future payment audit introduced with the provider integration. It must never revive the order or take inventory from a later buyer. Any verified void, reversal, or refund remains Task 16/provider-policy work; Task 13 deliberately adds no payment record or promise.

## Deferred gates

Post-payment cancellation and refund mechanics, delivery confirmation, mandatory
rights, return and dispute rules, failed/refused parcel outcomes, settlement,
payout, and ordinary admin tooling remain provider/counsel-owned. The seller
handoff engineering default is five calendar days, but its customer remedy and
money outcome remain blocked.
