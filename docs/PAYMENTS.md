# Payments

POK Payments is the intended first provider, not a permanent domain dependency. No production payment flow is implemented in Phase 1.

## Internal design

The commerce domain will own order totals, payment intent, normalized payment state, allowed transitions, and settlement eligibility. A provider adapter will own authentication, POK payloads, response/status mapping, retries, and webhook verification.

Expected internal records:

- `payments`: order, provider/reference, integer amount/currency, normalized status, timestamps, safe provider metadata
- `payment_events`: immutable provider event/reference, payload digest/safe payload, receipt time, processing result
- immutable financial entries for buyer charge, platform/buyer fees, shipping, seller payable, refunds, payout/reversal

This design will be created with the first real commerce use case, not as empty tables/interfaces.

## Safety invariants

- Seller listing and selling fees are fixed at zero; payment implementation cannot introduce them through client or provider input.
- Buyer fee policy `buyer_fee_none_v1` is fixed at EUR 0 with no tax and a EUR 0 refund. Future buyer charges require a separately approved policy version.
- Server derives EUR amount, fees, and order linkage; clients cannot select a currency.
- Card numbers, CVV, raw reusable card data, keys, and secrets are never persisted/logged.
- Create/capture/cancel/refund/webhook operations require idempotency keys or equivalent local uniqueness.
- Provider events may duplicate, delay, retry, or arrive out of order.
- Frontend success callbacks are UX signals only; backend retrieval/webhook confirmation controls payment state.
- Settlement and Buyer Protection claims must match licensed-provider and legal capabilities.
- No Buyer Protection proposition is currently approved.
- Direct buyer-to-seller bank transfer is prohibited. A buyer transfer is a candidate only to a platform/provider-controlled account with an immutable unique order reference and authoritative retrieval/reconciliation. Screenshots, receipts, and buyer/seller assertions are not payment proof.
- Sellers are not asked for bank statements. Prefer provider-hosted onboarding, KYC, and payout details; any later RISHIT-stored payout coordinates are a Task 23 privacy/security decision.

## Candidate methods

- **Instant online payment:** primary candidate; a future local reservation lasts
  15 minutes and verified provider evidence alone confirms payment.
- **Platform-account bank transfer:** acceptable candidate only when ownership,
  EUR processing, unique references, authoritative retrieval, reconciliation,
  refunds, privacy, and support are verified. Its approved engineering default is
  a 24-hour reservation because it is not assumed to share the online timeout.
- **Cash on Delivery:** launch candidate only, disabled until a selected courier
  confirms C2C service, EUR pricing, APIs/reconciliation, cash collection and
  remittance, failed/refused delivery, returns, liability, privacy, and support.

Reservation expiry, retry, cancellation, late-confirmation, and bilingual wording
are authoritative in [FIXED-PRICE-POLICY.md](FIXED-PRICE-POLICY.md).

## Open decisions

POK marketplace approval, seller/KYC/account requirements, provider split/refund behavior, authorization hold duration, capture/payout timing, funding-method enablement, any future Buyer Protection proposition, dispute period, tax/fiscal treatment, and Kosovo support.

See [POK-MARKETPLACE-RESEARCH.md](POK-MARKETPLACE-RESEARCH.md) for provider evidence and [LEGAL-READINESS.md](LEGAL-READINESS.md) for the counsel/provider decision gates and prohibited public claims.
