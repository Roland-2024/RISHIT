# POK Marketplace Research

Research date: 2026-08-11. Production integration is blocked on the confirmations below, not on Phase 1 application work.

Official sources:

- [POK integration documentation](https://docs.pokpay.io/)
- [POK complete machine-readable documentation](https://docs.pokpay.io/llms-full.txt)
- [POK Payments API reference](https://payments.doc.pokpay.io/)

## Confirmed technical capabilities

- Production and staging bases are `https://api.pokpay.io` and `https://api-staging.pokpay.io`; credentials are environment-specific.
- Backend login exchanges `keyId`/`keySecret` for a bearer token. Secrets stay server-side.
- Merchant SDK orders accept amount/products, `currencyCode`, `autoCapture`, shipping cost, redirects, `webhookUrl`, custom reference, expiry minutes, and optional `splitWith`.
- `autoCapture=false` supports later capture; cancel releases frozen funds. Capture accepts an amount up to the order total. Multiple-capture semantics are not documented.
- Refund endpoint supports an optional `refundAmount`; omission indicates a full refund.
- Detailed order retrieval can include transaction information, provider payment flows, captured/refunded/completion fields, and merchant custom reference.
- POK publishes React, React Native, vanilla JavaScript, CDN, PHP SDK, and beta Flutter integration paths. 3-D Secure is part of card SDK flows.

## Required research checklist

| Question | Current evidence / conclusion |
| --- | --- |
| 1. RISHIT currency | RISHIT processes EUR only. Documented ALL capability is outside the product scope. |
| 2. EUR support | Current REST quick-start uses `EUR`. Production merchant enablement: **REQUIRES POK CONFIRMATION**. |
| 3. Merchant account currency requirements | Split recipient by `merchantId` must have an account in the order currency. Primary merchant conversion/account rules: **REQUIRES POK CONFIRMATION**. |
| 4. Authorization behavior | `autoCapture=false` creates a confirm-then-capture/cancel flow described as frozen funds. Scheme/legal semantics: **REQUIRES POK CONFIRMATION**. |
| 5. `autoCapture` | Required boolean on order creation; false enables explicit capture. Confirmed. |
| 6. Capture behavior | Capture endpoint accepts `amount <= order total` and optional split. Partial/multiple/final capture rules: **REQUIRES POK CONFIRMATION**. |
| 7. Authorization expiration | Order creation supports `expiresAfterMinutes`; authorization-hold lifetime after confirmation is not stated. **REQUIRES POK CONFIRMATION**. |
| 8. Refunds | Full refund supported by omitting amount. Confirmed. |
| 9. Partial refunds | `refundAmount` is documented. Repeated/limit semantics: **REQUIRES POK CONFIRMATION**. |
| 10. Webhooks | Order creation accepts `webhookUrl`. Event catalogue/delivery contract: **REQUIRES POK CONFIRMATION**. |
| 11. Webhook verification | No signature or secret verification scheme was found. **REQUIRES POK CONFIRMATION**. |
| 12. Reconciliation | Detailed retrieval, transaction loading, order search/custom reference support technical reconciliation. Settlement reports and source-of-truth procedure: **REQUIRES POK CONFIRMATION**. |
| 13. `splitWith` | Order creation documents one split recipient by merchant ID or Albanian phone and an amount below total; capture also documents a merchant split. Confirmed API shape only. |
| 14. Marketplace compatibility | API shape does not establish regulated marketplace/settlement approval. **REQUIRES POK CONFIRMATION**. |
| 15. Seller POK accounts | Merchant-ID recipient needs an account in order currency; phone-recipient meaning/onboarding is unclear. **REQUIRES POK CONFIRMATION**. |
| 16. Seller merchant status | Not established. **REQUIRES POK CONFIRMATION**. |
| 17. Seller KYC | Not documented. **REQUIRES POK CONFIRMATION**. |
| 18. Seller payout behavior | Not documented as a marketplace payout lifecycle. **REQUIRES POK CONFIRMATION**. |
| 19. Delay payout until delivery | Not documented. Do not equate manual capture with escrow. **REQUIRES POK CONFIRMATION**. |
| 20. Refund split transactions | Not documented. **REQUIRES POK CONFIRMATION**. |
| 21. Platform fee collection | Split amount and commission fields exist, but commercial/legal fee routing is not established. **REQUIRES POK CONFIRMATION**. |
| 22. Albania requirements | Albanian locale and `+355` phone examples exist; onboarding, fiscal, KYC, and marketplace requirements remain **REQUIRES POK/LEGAL CONFIRMATION**. |
| 23. Kosovo requirements | No conclusive Kosovo support/account/compliance evidence found. **REQUIRES POK CONFIRMATION**. |
| 24. Flutter SDK | `pok_payments_flutter` is documented as beta (`^0.0.1`), native iOS/Android only, with composed widgets/token flow and native 3DS; not for Flutter web. |
| 25. Web integration | React, vanilla JS, CDN and server-side PHP/REST paths are documented. Backend creates orders; browser receives order/tokenization/3DS data, never merchant secrets. |

## Additional blockers

- Confirm whether API `amount` values are major or minor EUR units; examples alone are insufficient.
- Obtain webhook schemas, signing/replay rules, retry schedule, event ordering guarantees, and stable event IDs.
- Obtain status/state mapping, API idempotency support, rate limits, timeouts, and maintenance/version policy.
- Obtain written marketplace approval and contractual settlement/refund/KYC responsibilities.

## Implementation stance

Do not install the SDK or write a POK adapter until a staging merchant and the blockers relevant to the fixed-price flow are answered. The first spike should use staging, backend-created orders, retrieval-based reconciliation, safe logs, and no settlement claims.
