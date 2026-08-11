# Orders

Orders are not implemented in Phase 1. This document fixes the invariants that Phase 3 must turn into tested code.

## Normal lifecycle

`CREATED → AWAITING_PAYMENT → PAID → AWAITING_SHIPMENT → SHIPPED → DELIVERED → COMPLETED`

Exceptional states may include cancelled, disputed, refund pending, refunded, return requested, and returned. Exact names will be finalized with payment/shipping behavior; invalid transitions must throw rather than silently rewrite state.

## Creation transaction

1. Lock the listing/accepted offer/auction outcome.
2. Re-check seller, buyer, listing availability, and authoritative price.
3. Calculate and snapshot the EUR item amount, fees, shipping, total, seller payable, and currency using integer cents.
4. Create the order and reserve/sell the unique item atomically.
5. Commit before initiating external payment work.

No second buyer may create a valid order for the same unique item. Database constraints and row locks must back this rule.

## Snapshots and audit

Orders retain the commercial facts needed after a listing/user changes: item title/description/condition/price, buyer/seller identities, addresses required for fulfillment, fee policy/version, shipping selection/price, and timestamps. Financial truth is also recorded in payment/events/ledger records, never inferred only from `orders.status`.

## Open decisions

Reservation timeout, payment retry policy, cancellation boundaries, shipment deadline, delivery confirmation/dispute window, return policy, settlement eligibility, and admin overrides.
