# Orders

The provider-neutral order foundation records authoritative commercial snapshots and normalized lifecycle transitions. It does not expose checkout or call payment or shipping providers.

## Normal lifecycle

`CREATED → AWAITING_PAYMENT → PAID → AWAITING_SHIPMENT → SHIPPED → DELIVERED → COMPLETED`

Only this documented forward lifecycle is implemented. Cancellation, dispute, refund, return, and administrative override transitions remain deferred until their policies and provider behavior are approved. Invalid transitions throw rather than silently rewriting state.

## Creation transaction

1. Lock the listing.
2. Re-check seller, buyer, listing availability, and authoritative price.
3. Calculate and snapshot the EUR item amount, fees, shipping, total, seller payable, and currency using integer cents.
4. Create the order and reserve/sell the unique item atomically.
5. Commit without initiating external payment work.

No second buyer may create a valid order for the same unique item. Database constraints and row locks must back this rule.

## Snapshots and audit

Orders retain the commercial facts needed after a listing/user changes: item title/description/condition/price, buyer/seller identities, buyer/seller addresses, fee policy/version, shipping price, and timestamps. Order amounts come from shared server-side calculation, never client totals. Payment and ledger records remain deferred until a real provider-backed use case exists; financial truth is never inferred only from `orders.state`.

Buyer fee policy `buyer_fee_none_v1` snapshots a EUR 0 fee, EUR 0 tax, EUR 0 display amount, and EUR 0 refundable amount. Seller listing and selling fees are also fixed at EUR 0. A future buyer policy requires a new approved version and cannot alter historical snapshots.

## Open decisions

Reservation timeout, payment retry policy, cancellation boundaries, shipment deadline, delivery confirmation/dispute window, return policy, settlement eligibility, and admin overrides.
