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
- A listing accepts at most eight JPG, PNG, or WebP images in explicit display order. Each upload is at most 8 MB and between 320 and 4096 pixels on each side, then decoded and re-encoded once without metadata; no additional image variants are generated.
- Availability checks, order creation, and reservation occur in a database transaction with locking/constraints; UI button state is never protection.
- State transitions are explicit and reject invalid reversals.
- Fixed-price reservation, expiry, cancellation, and listing outcomes follow versioned policy [`fixed_price_v1`](FIXED-PRICE-POLICY.md). Instant online payment uses 15 minutes; an approved platform-account bank transfer would use 24 hours; retries never extend either deadline.
- Due reservations are rechecked under a row lock by an idempotent one-minute cleanup. Late payment confirmation enters reconciliation and never revives released inventory.
- Before authoritative payment, the buyer, system, or an audited admin may cancel as defined by policy; the seller cannot cancel directly. Post-payment and post-handoff cancellation requires the later verified provider/legal workflow.
- Task 13 enables only internal `fixed_price_v1_online` reservations. Platform-account bank transfer and COD profiles remain disabled until their roadmap gates close.
- Exact live creation retries return the same order and never extend the deadline. A reused key with different inputs or a stale released/deadline-passed retry fails.
- The database permits one active inventory claim per listing. Cancelled and expired rows remain auditable with a cleared claim, allowing a later reservation only after conditional release.
- Release reactivates only a still-reserved, undeleted, positive-price EUR listing. Seller-hidden/deleted or admin-hidden inventory is never automatically republished.

## Payments and settlement

- RISHIT charges sellers EUR 0 to list and EUR 0 when an item sells.
- Buyer fee policy `buyer_fee_none_v1` charges EUR 0, has no tax, displays EUR 0, and refunds EUR 0. A future buyer charge requires a separately approved version and cannot rewrite historical orders.
- Backend-calculated totals and verified provider events are authoritative.
- Authoritative payment evidence received after reservation release leaves the terminal order and current listing unchanged and enters the future provider-backed reconciliation audit; it cannot displace a later buyer. Client callbacks and participant assertions are never payment truth.
- Card data and reusable raw card payloads are never stored or logged.
- Duplicate, delayed, retried, and out-of-order provider events are expected.
- RISHIT must not claim escrow or legal custody of funds without provider and legal confirmation.
- No Buyer Protection proposition is approved. Seller settlement timing, KYC, and provider split/refund behavior remain unresolved.
- Direct buyer-to-seller bank transfer is prohibited. A buyer bank transfer may be considered only to a platform/provider-controlled account with a unique order reference and authoritative retrieval/reconciliation; screenshots and user assertions are never proof.
- COD is a disabled launch candidate until a courier verifies C2C support, EUR pricing, API/reconciliation, collection/remittance, failed/refused delivery, returns, liability, privacy, and support.
- Prefer provider-hosted seller onboarding/KYC/payout details. Never request seller bank statements. If payout coordinates must later be stored, Task 23 must minimize, encrypt, mask, authorize, retain, and audit them.

## Shipping

- Shipping prices and service choices are snapshotted on the order.
- Provider-specific statuses map to internal shipment states.
- Client or seller assertions cannot directly mark a shipment delivered.
- Courier, pricing, returns, and cross-border rules remain unresolved.
- The engineering default is courier handoff within five calendar days of verified payment, or approved COD acceptance. Seller non-shipment enters the appropriate verified cancellation/refund path; the listing stays hidden until availability is confirmed.

## Auctions

- Seller self-bidding, late bids, suspended/ineligible bidders, and insufficient bids are rejected server-side.
- Bids use database transactions and row locks.
- Initial duration options are 24, 72, and 168 hours.
- A valid bid in the final 120 seconds extends the auction by 120 seconds; both values are configurable.
- Hidden reserves and proxy bidding are outside the first auction MVP.
