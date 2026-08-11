# Business Rules

## Identity and trust

- One user account can buy, sell, or do both; there are no buyer/seller account types.
- Reviews must eventually reference a completed marketplace order.
- Higher-risk selling, bidding, payout, or value thresholds may require additional verification; exact policy is unresolved.

## Money

- The checked-in EUR decision supersedes older planning-prompt requirements for ALL. ALL is supported only as identifiable legacy data, not for new public listings or marketplace transactions.
- New listings and all future marketplace transactions use EUR only.
- New listing prices must be greater than zero.
- All internal amounts are signed integer cents with explicit `EUR` currency; `5200` means `€52.00`.
- Historical non-EUR records are preserved for audit and owner access, excluded from public discovery, and never relabeled or converted without an explicit migration policy.
- No financial calculation uses floating point.
- Albanian and English presentation use the same EUR process.
- Orders snapshot item price, fees, shipping, total, seller payable, and currency. Later configuration/exchange-rate changes cannot rewrite history.
- Provider amount units must be confirmed and explicitly mapped before POK integration.

## Inventory and orders

- A second-hand listing represents one unique item unless explicitly modeled otherwise.
- Catalog listings have explicit `active`, `hidden`, or `sold` status; only active EUR listings appear publicly.
- Hidden, sold, soft-deleted, and legacy non-EUR listings remain available only where an authorized owner flow explicitly needs them; they never appear in public discovery.
- Sellers alone can edit, hide, republish, or soft-delete their listings.
- Listing condition uses a controlled set; seller descriptions must disclose wear and flaws.
- A listing accepts at most eight validated JPG, PNG, or WebP images in explicit display order.
- Availability checks, order creation, and reservation occur in a database transaction with locking/constraints; UI button state is never protection.
- State transitions are explicit and reject invalid reversals.

## Payments and settlement

- RISHIT charges sellers EUR 0 to list and EUR 0 when an item sells.
- Backend-calculated totals and verified provider events are authoritative.
- Card data and reusable raw card payloads are never stored or logged.
- Duplicate, delayed, retried, and out-of-order provider events are expected.
- RISHIT must not claim escrow or legal custody of funds without provider and legal confirmation.
- Seller settlement timing, KYC, Buyer Protection or other buyer-side fees, and split/refund behavior remain unresolved.

## Shipping

- Shipping prices and service choices are snapshotted on the order.
- Provider-specific statuses map to internal shipment states.
- Client or seller assertions cannot directly mark a shipment delivered.
- Courier, pricing, returns, and cross-border rules remain unresolved.

## Auctions

- Seller self-bidding, late bids, suspended/ineligible bidders, and insufficient bids are rejected server-side.
- Bids use database transactions and row locks.
- Initial duration options are 24, 72, and 168 hours.
- A valid bid in the final 120 seconds extends the auction by 120 seconds; both values are configurable.
- Hidden reserves and proxy bidding are outside the first auction MVP.
