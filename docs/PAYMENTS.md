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
- Server derives EUR amount, fees, and order linkage; clients cannot select a currency.
- Card numbers, CVV, raw reusable card data, keys, and secrets are never persisted/logged.
- Create/capture/cancel/refund/webhook operations require idempotency keys or equivalent local uniqueness.
- Provider events may duplicate, delay, retry, or arrive out of order.
- Frontend success callbacks are UX signals only; backend retrieval/webhook confirmation controls payment state.
- Settlement and Buyer Protection claims must match licensed-provider and legal capabilities.

## Open decisions

POK marketplace approval, seller/KYC/account requirements, split/refund behavior, authorization hold duration, capture/payout timing, buyer-side funding, Buyer Protection fee, dispute period, tax/fiscal treatment, and Kosovo support.

See [POK-MARKETPLACE-RESEARCH.md](POK-MARKETPLACE-RESEARCH.md) for provider evidence and [LEGAL-READINESS.md](LEGAL-READINESS.md) for the counsel/provider decision gates and prohibited public claims.
