# Orders

The provider-neutral order foundation records authoritative commercial snapshots and normalized lifecycle transitions. It does not expose checkout or call payment or shipping providers.

## Implemented normal lifecycle

`CREATED → AWAITING_PAYMENT → PAID → AWAITING_SHIPMENT → SHIPPED → DELIVERED → COMPLETED`

Only this documented forward lifecycle is implemented. Cancellation, dispute, refund, return, and administrative override transitions remain deferred until their policies and provider behavior are approved. Invalid transitions throw rather than silently rewriting state.

The approved future reservation behavior is defined in
[Fixed-Price Policy Decision Record](FIXED-PRICE-POLICY.md). Documentation of a
future state does not mean that the current enum or application action implements
it.

## Creation transaction

1. Lock the listing.
2. Re-check seller, buyer, listing availability, and authoritative price.
3. Calculate and snapshot the EUR item amount, fees, shipping, total, seller payable, and currency using integer cents.
4. Create the order and make the unique item unavailable atomically.
5. Commit without initiating external payment work.

No second buyer may create a valid order for the same unique item. Database constraints and row locks must back this rule.

## Snapshots and audit

Orders retain the commercial facts needed after a listing/user changes: item title/description/condition/price, buyer/seller identities, buyer/seller addresses, fee policy/version, shipping price, and timestamps. Order amounts come from shared server-side calculation, never client totals. Payment and ledger records remain deferred until a real provider-backed use case exists; financial truth is never inferred only from `orders.state`.

Buyer fee policy `buyer_fee_none_v1` snapshots a EUR 0 fee, EUR 0 tax, EUR 0 display amount, and EUR 0 refundable amount. Seller listing and selling fees are also fixed at EUR 0. A future buyer policy requires a new approved version and cannot alter historical snapshots.

## Approved reservation policy for Task 13

- Instant online payment reserves for 15 minutes from the committed local
  reservation. Provider-session recovery and payment retries do not extend it.
- A future approved platform-account bank transfer reserves for 24 hours from
  issue of its unique order reference. Direct buyer-to-seller transfer is
  prohibited.
- A future approved COD order starts its reservation when the server accepts the
  courier-backed COD method and has no prepayment timeout; the five-calendar-day
  handoff deadline applies.
- An idempotent scheduler checks expirations every minute under a row lock. The
  next run retries failed cleanup, and three consecutive failed runs alert
  operations.
- Before verified payment, the buyer may cancel, the system may expire/cancel a
  definitive failure, and an admin may cancel with an audited reason. The seller
  cannot cancel directly and may only report an unavailable item.
- `expired` and pre-payment `cancelled` attempts remain for audit. An eligible
  listing may reactivate after buyer/system expiry or cancellation; an item
  reported unavailable becomes `hidden`.
- Late payment confirmation never revives the order or takes inventory from a
  later buyer. It enters reconciliation.

Task 13 may implement only reservation, expiry, pre-payment cancellation,
conditional listing release, and their concurrency/audit rules. It must not add
checkout, provider calls, payment, refund, shipment, return, dispute, settlement,
or payout behavior.

## Deferred gates

Post-payment cancellation and refund mechanics, delivery confirmation, mandatory
rights, return and dispute rules, failed/refused parcel outcomes, settlement,
payout, and ordinary admin tooling remain provider/counsel-owned. The seller
handoff engineering default is five calendar days, but its customer remedy and
money outcome remain blocked.
