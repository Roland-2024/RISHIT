# POK Marketplace Validation

Audit date: 2026-08-11. This is a documentation audit, not staging evidence, provider approval, or legal advice. It does not close roadmap Task 15 or blocker `B-PAY`.

## Decision

**NO-GO for a staging spike today.** POK publishes a staging environment and test cards, but RISHIT does not yet have confirmed amount units, written marketplace approval, seller/KYC and split-settlement rules, a webhook delivery contract, or provisioned staging merchant and seller test entities. Those gaps affect money correctness and the legal/operating model and should not be guessed in code.

Change the staging-spike decision to **GO** only after POK:

1. answers the critical questions in the draft below in writing;
2. confirms RISHIT's Albania C2C marketplace model and EUR setup;
3. supplies staging-only credentials plus representative merchant/seller test accounts; and
4. supplies the webhook security/delivery contract or confirms that retrieval is the required source of truth.

The eventual spike should be limited to evidence gathering: authenticate, create/retrieve a EUR order, authorize, capture/cancel, perform full/partial refunds including a split case, observe webhooks, and reconcile every result by retrieval. It must not add a production adapter, SDK, production credentials, or customer data.

## Evidence standard and primary sources

Only explicit statements in current official POK sources are treated as confirmed. A request/response example proves only that POK published that example; it does not by itself prove supported currencies, amount units, commercial eligibility, or marketplace behavior. An API field proves its documented API shape, not regulatory approval or settlement semantics.

- [POK documentation index](https://docs.pokpay.io/llms.txt)
- [REST integration guide](https://docs.pokpay.io/docs/rest-api.md)
- POK Payments API: [create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2), [capture](https://payments.doc.pokpay.io/#a3a516f5-e069-4fa4-9a21-d2217484b63c), [refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0), [detailed retrieval](https://payments.doc.pokpay.io/#6ecf27df-3e7e-427b-b5f3-d593fe1eec52), and [cancel](https://payments.doc.pokpay.io/#043e86ba-237a-4291-9209-93831420fbdc)
- [Web SDK documentation](https://docs.pokpay.io/docs/pok-js.md)
- [Flutter SDK documentation](https://docs.pokpay.io/docs/flutter.md)
- [POK Business portal](https://merchant.pokpay.io/) and [POK privacy policy](https://pokpay.io/privacy-policy)

## Audit findings

`REQUIRES POK CONFIRMATION` means the reviewed official sources do not answer the production question.

| Area | Finding |
| --- | --- |
| EUR merchant enablement | The REST quick-start publishes an `EUR` sample, but the API contract describes `currencyCode` only as a string and does not list supported merchant currencies. EUR acceptance for RISHIT's production and staging merchants is **REQUIRES POK CONFIRMATION**. [REST guide](https://docs.pokpay.io/docs/rest-api.md) · [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) |
| Amount units | `amount`, product price, shipping, capture, split, and refund fields are documented as numbers without defining major/minor units or decimal precision. The fact that an example uses `100` does not establish whether it means EUR 100 or EUR 1.00. **REQUIRES POK CONFIRMATION**. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) · [API refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0) |
| Marketplace/platform approval | `splitWith` is an API field, not written approval for a C2C marketplace, merchant-of-record model, custody, delayed seller release, or platform fee. All of those are **REQUIRES POK CONFIRMATION** and qualified Albanian legal review. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) |
| Split recipient account | Create documents one recipient by `merchantId` or `+355` user phone. A merchant-ID recipient must have an account in the order currency. What a phone recipient must hold, and whether either path is approved for RISHIT sellers, is **REQUIRES POK CONFIRMATION**. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) |
| Seller merchant and KYC requirements | POK's privacy policy says POK performs identity/KYC checks generally, but it does not define individual seller onboarding, merchant status, KYB/KYC evidence, age/residency limits, thresholds, or ongoing checks for marketplace recipients. **REQUIRES POK CONFIRMATION**. [POK privacy policy](https://pokpay.io/privacy-policy) |
| Authorization and `autoCapture` | Confirmed API shape: create requires `autoCapture`; `false` permits a later capture after the client confirms the order. The exact scheme/legal characterization of the hold is **REQUIRES POK CONFIRMATION**. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) · [API capture](https://payments.doc.pokpay.io/#a3a516f5-e069-4fa4-9a21-d2217484b63c) |
| Capture | Confirmed API shape: capture requires an amount less than or equal to the order total and may include a merchant split. Minimums, partial-capture finality, multiple captures, deadlines, failure recovery, and capture/currency rounding rules are **REQUIRES POK CONFIRMATION**. [API capture](https://payments.doc.pokpay.io/#a3a516f5-e069-4fa4-9a21-d2217484b63c) |
| Expiry | Create documents `expiresAfterMinutes` only as the time after which an unpaid SDK order can no longer be paid. Authorization-hold lifetime after confirmation and the capture deadline are **REQUIRES POK CONFIRMATION**. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) |
| Cancellation | Confirmed API shape: an `autoCapture=false` order can be cancelled after confirmation and the reference says this releases frozen funds. Eligible states, timing, asynchronous failure behavior, and authoritative terminal status are **REQUIRES POK CONFIRMATION**. [API cancel](https://payments.doc.pokpay.io/#043e86ba-237a-4291-9209-93831420fbdc) |
| Full refunds | Confirmed API shape: omitting `refundAmount` requests a full refund. Eligibility windows, processing time, fee treatment, terminal states, and failure/reversal handling are **REQUIRES POK CONFIRMATION**. [API refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0) |
| Partial refunds | Confirmed API shape: supplying a positive `refundAmount` requests a partial refund. Maximum cumulative amount, repeated refunds, concurrency, rounding, and completion semantics are **REQUIRES POK CONFIRMATION**. [API refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0) |
| `splitWith` behavior | Confirmed API shape only: create can split an amount smaller than the order amount; capture can include one merchant recipient and amount. Timing, atomicity, commission/platform-fee use, recipient notification, insufficient-balance behavior, and whether create-time and capture-time splits must match are **REQUIRES POK CONFIRMATION**. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) · [API capture](https://payments.doc.pokpay.io/#a3a516f5-e069-4fa4-9a21-d2217484b63c) |
| Refunds after a split | The refund endpoint documents no split allocation, recipient debit, insufficient-balance, fee reversal, or platform liability behavior. **REQUIRES POK CONFIRMATION**. [API refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0) |
| Seller settlement/payout timing | The reviewed API documentation does not define marketplace seller balances, settlement eligibility, payout rails, schedules, reserves, reversals, reports, or delayed release after delivery. **REQUIRES POK CONFIRMATION**. Do not describe manual capture as escrow. |
| Webhook existence | Confirmed API shape only: create accepts an optional `webhookUrl`. That does not establish any event or delivery contract. [API create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2) |
| Webhook events and security | Event catalogue, payload schemas, signing/authentication, key rotation, replay protection, source IP guidance, retry schedule, timeout, duplicate delivery, ordering guarantees, stable event IDs, and retention/replay are all **REQUIRES POK CONFIRMATION**. |
| Idempotency | The web SDK says a network retry against the same order ID is idempotent. The API reference documents no idempotency key or duplicate-request contract for create, capture, cancel, or refund, so server-side mutation idempotency is **REQUIRES POK CONFIRMATION**. [Web SDK](https://docs.pokpay.io/docs/pok-js.md) · [API reference](https://payments.doc.pokpay.io/) |
| Reconciliation | Detailed retrieval by known merchant/order ID can return transaction ID, merchant custom reference, completion/refund flags, commissions, and optional transaction/payment-flow detail. The published collection exposes no documented list/search/export or settlement report, so the source-of-truth procedure, status catalogue, report cadence, and historical retention are **REQUIRES POK CONFIRMATION**. [Detailed retrieval](https://payments.doc.pokpay.io/#6ecf27df-3e7e-427b-b5f3-d593fe1eec52) |
| Rate limits and service contract | Limits, response headers, backoff, request timeouts, availability targets, maintenance notice, API versioning, and deprecation policy are **REQUIRES POK CONFIRMATION**. |
| Staging access | Confirmed: POK publishes `https://api-staging.pokpay.io`, staging-only test cards, and environment-specific credentials. RISHIT credential provisioning, merchant/seller test entities, feature parity, data reset, and support path are **REQUIRES POK CONFIRMATION**. [REST guide](https://docs.pokpay.io/docs/rest-api.md) |
| Albania availability | POK publicly offers business online-payment services and identifies RPay as registered in Albania and licensed there as an electronic-money institution. RISHIT's merchant underwriting, C2C marketplace approval, seller eligibility, and contract terms are still **REQUIRES POK CONFIRMATION** and legal review. [POK Business](https://merchant.pokpay.io/) · [POK privacy policy](https://pokpay.io/privacy-policy) |
| Kosovo availability | A Kosovo phone-country option or an international card-payment statement does not prove merchant acquiring, seller accounts, settlement, or marketplace availability in Kosovo. All are **REQUIRES POK CONFIRMATION** and separate Kosovo legal review. |
| Web support | Confirmed technical surface: POK documents React, vanilla JavaScript/npm, CDN, React Native, server-side PHP, and raw REST paths. Backend order creation and merchant credentials remain server-side. This confirms integration options, not RISHIT commercial approval. [Documentation index](https://docs.pokpay.io/llms.txt) · [Web SDK](https://docs.pokpay.io/docs/pok-js.md) |
| Flutter support | Confirmed technical surface: `pok_payments_flutter` is documented as beta (`^0.0.1`) for native iOS and Android, with composed widgets and an imperative token flow. It requires native modules and explicitly does not support Flutter web. [Flutter SDK](https://docs.pokpay.io/docs/flutter.md) |

## Draft provider questionnaire — not sent

**Subject:** RISHIT C2C marketplace — POK Payments approval and staging questions

Hello POK Payments team,

RISHIT is evaluating POK for an Albania-first C2C second-hand fashion marketplace. Buyers would pay RISHIT checkout totals in EUR; sellers pay no listing or selling fee; Kosovo may follow later. Before a staging spike, please answer the following in writing and link the governing API documentation or contract terms where possible:

1. Do you approve this C2C marketplace/platform model, and who would be merchant of record and responsible for refunds, disputes, KYC/AML, tax/fiscal duties, and chargebacks?
2. Can our staging and production merchants process EUR? For every order/product/shipping/capture/split/refund field, are amounts major units or minor units, what decimal precision is accepted, and how is rounding handled?
3. Must each seller hold a POK user account, currency account, or merchant account? What KYC/KYB, age, residency, Albania/Kosovo, threshold, and ongoing-verification rules apply to sellers and RISHIT?
4. For `autoCapture=false`, what starts the authorization, how long is the hold valid, when must capture occur, are partial or multiple captures supported, and what are the exact cancellation and expiry states?
5. What are the rules, limits, timing, identifiers, fees, and failure states for full, partial, and repeated refunds?
6. For `splitWith`, when do funds become available to the recipient, can release wait until delivery/dispute expiry, can RISHIT collect a platform amount, and how are refunds, chargebacks, insufficient recipient balances, reversals, and reports handled?
7. Please provide webhook event names, payload schemas, stable event IDs, signature/authentication and key-rotation rules, replay protection, retry schedule, timeouts, duplicate and ordering guarantees, IP guidance, and replay/retention options.
8. Which API operations support idempotency keys? Please provide the complete status/state catalogue, recommended reconciliation procedure, retrieval/search/export and settlement reports, rate limits, timeouts, availability/support targets, versioning, and deprecation policy.
9. Can you provision staging-only RISHIT merchant credentials and representative seller/user/merchant recipients, including EUR, split, authorization/capture/cancel, refund, webhook, and failure scenarios? Is staging behavior feature-equivalent to production?
10. Please confirm online-payment, marketplace, seller-recipient, and settlement availability separately for Albania and Kosovo, plus the required agreements and onboarding contacts. Please also confirm production support status for web and the beta Flutter iOS/Android SDK.

Thank you.
