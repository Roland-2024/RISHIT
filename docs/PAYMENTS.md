# Payments

POK Payments is the intended first provider, not a permanent domain dependency. No production payment flow is implemented in Phase 1.

As of 2026-08-12, both POK online payment and platform-account bank
transfer are **NO-GO** for Task 16. No method has sufficient provider,
staging, operational, and Albanian legal evidence. See the dated
[payment-method validation](POK-MARKETPLACE-RESEARCH.md).

## Internal design

The commerce domain will own order totals, payment intent, normalized payment state, allowed transitions, and settlement eligibility. A provider adapter will own authentication, POK payloads, response/status mapping, retries, and webhook verification.

Expected internal records:

- `payments`: order, provider/reference, integer amount/currency, normalized status, timestamps, safe provider metadata
- `payment_events`: immutable provider event/reference, payload digest/safe payload, receipt time, processing result
- immutable financial entries for buyer charge, platform/buyer fees, shipping, seller payable, refunds, payout/reversal

This design will be created with the first real commerce use case, not as empty tables/interfaces.

## Safety invariants

- Seller listing and selling fees are independently calculated and snapshotted as exactly EUR 0; payment implementation cannot introduce them through client or provider input.
- Buyer-side fees remain unresolved. The existing internal snapshot does not approve a checkout fee, tax, display amount, refund treatment, or public fee claim; provider integration cannot invent them before an approved version is documented.
- The shared order calculator derives signed integer EUR-cent item, shipping, fee, total, and seller-payable amounts and snapshots the policy/version. Persisted totals must match that calculation, and historical snapshots never follow later configuration.
- Clients may select only currently valid server-provided choice identifiers. They cannot select a currency or submit a price, fee, shipping amount, seller payable, or total as financial truth.
- Card numbers, CVV, raw reusable card data, keys, and secrets are never persisted/logged.
- Create/capture/cancel/refund/webhook operations require idempotency keys or equivalent local uniqueness.
- Provider events may duplicate, delay, retry, or arrive out of order.
- Frontend success callbacks are UX signals only; backend retrieval/webhook confirmation controls payment state.
- Settlement and Buyer Protection claims must match licensed-provider and legal capabilities.
- No Buyer Protection proposition is currently approved.
- Direct buyer-to-seller bank transfer is prohibited. A buyer transfer is a candidate only to a platform/provider-controlled account with an immutable unique order reference and authoritative retrieval/reconciliation. Screenshots, receipts, and buyer/seller assertions are not payment proof.
- Sellers are not asked for bank statements. Prefer provider-hosted onboarding, KYC, and payout details; any later RISHIT-stored payout coordinates are a Task 23 privacy/security decision.

## Candidate methods

| Method | Status | Boundary |
| --- | --- | --- |
| POK instant online payment | **NO-GO** | Public API shapes and a staging host exist, but written marketplace approval, EUR units, seller/KYC, split/settlement, webhook, idempotency, reconciliation, rate-limit, legal, and authorized staging evidence are incomplete. A future local reservation would last 15 minutes; only authenticated provider evidence can confirm payment. |
| Platform/provider-account bank transfer | **NO-GO** | No named provider/account has verified recipient ownership, EUR rails, unique references, authoritative incoming-payment retrieval/events, timing/finality, refunds/reversals, fees, reconciliation, data roles, or Albania marketplace legality. The disabled engineering default is a 24-hour reservation. |
| Direct buyer-to-seller bank transfer | **FORBIDDEN** | It is not part of RISHIT. Screenshots and participant assertions are never proof. |
| Cash on Delivery | **NO-GO** | Disabled until a selected courier confirms C2C EUR service, API/reconciliation, cash collection/remittance, failed/refused delivery, returns, liability, privacy, and support. |

Reservation expiry, retry, cancellation, late-confirmation, and bilingual wording
are authoritative in [FIXED-PRICE-POLICY.md](FIXED-PRICE-POLICY.md).

## Open decisions

Buyer-side fee policy; POK marketplace approval; seller/KYC/account requirements; provider split/refund behavior; authorization hold duration; capture/payout timing; funding-method enablement; any future Buyer Protection proposition; dispute period; tax/fiscal treatment; and Kosovo support.

See [POK-MARKETPLACE-RESEARCH.md](POK-MARKETPLACE-RESEARCH.md) for provider evidence and [LEGAL-READINESS.md](LEGAL-READINESS.md) for the counsel/provider decision gates and prohibited public claims.
