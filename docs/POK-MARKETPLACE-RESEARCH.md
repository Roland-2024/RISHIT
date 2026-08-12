# Payment Method Validation

Audit date: 2026-08-12. This is a primary-source and evidence audit, not legal
advice or provider approval. No production adapter, SDK, interface, or payment
flow was added.

## Decisions

| Method | Staging spike | Task 16 | Decision |
| --- | --- | --- | --- |
| POK online card payment | **NO-GO** | **NO-GO** | Public documentation exposes useful API shapes and staging hosts, but there are no written RISHIT marketplace answers, authorized RISHIT staging credentials, confirmed EUR amount units, seller test entities, webhook contract, or complete settlement/reconciliation evidence. |
| Platform/provider-account buyer bank transfer | **NO-GO** | **NO-GO** | No bank or regulated provider, recipient account owner, EUR account, unique-reference contract, authoritative incoming-payment feed, refund/reversal procedure, commercial terms, data-role allocation, or Albanian counsel approval has been selected or evidenced. |
| Direct buyer-to-seller transfer | **FORBIDDEN** | **FORBIDDEN** | This is outside the product. A screenshot, receipt, buyer assertion, seller assertion, or seller bank statement is never payment evidence. |

Task 16 must not start for either candidate method. A method can change to
**GO** only when its method-specific provider, staging, operational, and legal
evidence below is checked in and every critical answer is explicit. Generic API
examples, provider licensing, and the existence of EUR payment rails are not a
substitute.

## Evidence inventory

| Evidence class | Result on 2026-08-12 |
| --- | --- |
| Written POK answers or commercial approval for RISHIT | **None supplied with this task and none found in the repository.** |
| POK contract/order form defining marketplace roles and money flow | **None found.** |
| Qualified Albanian legal opinion for either method | **None found.** |
| Authorized POK staging credentials and representative merchant/seller entities | **Unavailable.** No matching `POK_*`, `POKPAY_*`, or `RPAY_*` key names were present in the process environment or local `.env`; values were not printed or recorded. |
| Staging transaction evidence | **Not run.** Testing without authorized credentials and agreed seller/split scenarios would not close any gate. |
| Bank-transfer provider/account/API evidence | **None supplied or found.** No bank-transfer staging spike was attempted. |

## Dated primary sources

All sources were retrieved on 2026-08-12.

| Source | What it establishes—and does not establish |
| --- | --- |
| [POK documentation index](https://docs.pokpay.io/llms.txt) and [full documentation bundle](https://docs.pokpay.io/llms-full.txt) | Official integration surfaces, staging host/test cards, and SDK guidance. The REST source reported `Last-Modified: Mon, 03 Aug 2026 10:19:10 GMT`. It does not approve RISHIT or define the missing marketplace contract. |
| [POK REST guide](https://docs.pokpay.io/docs/rest-api.md) | Authentication and create/retrieve examples for staging and production. The published `EUR` example proves only the example, not merchant enablement or units. |
| POK Payments API: [create](https://payments.doc.pokpay.io/#76c1aa64-6379-4936-8b3b-4d89e0138fe2), [capture](https://payments.doc.pokpay.io/#a3a516f5-e069-4fa4-9a21-d2217484b63c), [refund](https://payments.doc.pokpay.io/#7cbf745a-2360-47d3-8335-4b740fb85fa0), [detailed retrieval](https://payments.doc.pokpay.io/#6ecf27df-3e7e-427b-b5f3-d593fe1eec52), and [cancel](https://payments.doc.pokpay.io/#043e86ba-237a-4291-9209-93831420fbdc) | The current public Postman collection exposes these operations and request/response fields. It does not document a complete webhook, idempotency, payout, or reconciliation contract. |
| [POK privacy policy](https://pokpay.io/privacy-policy) | RPAY identifies itself as the controller for POK services and describes identity/KYC, payment, transfer, refund, settlement, and regulatory processing in general. It does not allocate roles for RISHIT or marketplace sellers. |
| Bank of Albania [current electronic-money-institution register](https://www.bankofalbania.org/rc/doc/List_of_electronic_money_institutions_33287.xlsx) | Lists `RPAY SH.P.K.`. A regulated-entity listing is not approval for RISHIT's proposed flow or proof of a specific licensed service/contract term. |
| Bank of Albania [Law 55/2020 On Payment Services](https://www.bankofalbania.org/rc/doc/Ligji_Per_sherbimet_e_pagesave_anglisht_18199.pdf) and [integrated Regulation 59/2021](https://www.bankofalbania.org/rc/doc/Regulation_no_59_dated_24_11_2021_on_licencing_of_PIs_and_EMIs_20359.pdf) | Establish the Albanian payment-services/licensing framework. They do not answer whether RISHIT may receive/control marketplace buyer funds or how the proposed parties must contract. |
| Bank of Albania [Decision 49/2025 on EUR transfers](https://www.bankofalbania.org/Payments/Legal_framework/Legal_framework/Decision_No_49_2025_On_the_approval_of_amendments_to_the_regulation_Establishing_requirements_for_credit_transfers_and_direct_debits_in_euro.html) and [first operational SEPA transaction notice](https://www.bankofalbania.org/Press/Governor_Sejko_Address_at_the_Launching_Ceremony_of_the_First_SEPA_Transaction_Albania.html) | Confirm regulated EUR credit-transfer infrastructure and operational SEPA transfers in Albania from 7 October 2025. They do not establish a usable marketplace collection/reconciliation product. |
| Albanian Parliament [Law 124/2024 On the Protection of Personal Data](https://e-legjislacioni.parlament.al/document/download?currentLanguage=alb&docNumber=124%2F2024&expressionUriThis=%2Fakn%2Fal%2Fact%2Fligj%2F2024-12-19%2F124%2Falb%40%2F%21main&extension=HTML) | Establishes the current data-protection framework and controller/processor concepts. The actual RISHIT/provider roles and data terms remain unallocated. |

## POK findings

`REQUIRES WRITTEN POK CONFIRMATION` means the reviewed official source does not
answer the production question. An API field proves only its documented shape.

| Area | Finding |
| --- | --- |
| EUR merchant enablement | The REST guide has an `EUR` sample, but no supported-currency matrix or RISHIT merchant approval was found. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Amount units and precision | Order, product, shipping, capture, split, and refund amounts are numbers, but the reviewed contract does not define major/minor units, decimal precision, rounding, minimums, or maximums. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Marketplace approval and roles | `splitWith` is not approval for a C2C marketplace, merchant-of-record arrangement, custody/safeguarding, platform commission, delayed seller release, or zero-seller-fee model. **REQUIRES POK AND COUNSEL CONFIRMATION.** |
| Seller onboarding and KYC | The privacy policy describes KYC generally. It does not define seller account type, hosted onboarding, KYB/KYC evidence, age/residency limits, thresholds, ongoing checks, rejection states, or RISHIT access. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Authorization | Create requires `autoCapture`; `false` exposes a later capture/cancel shape. Hold start, characterization, validity, extension, expiry, and authoritative states are not defined. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Capture | Capture accepts an amount no greater than the order total and may include a split. Partial/multiple-capture rules, deadline, finality, rounding, failures, and recovery are not fully defined. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Cancel | Cancel is documented for a confirmed `autoCapture=false` order and described as releasing frozen funds. Eligible states, timing, response finality, asynchronous failure, and retrieval state are incomplete. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Full and partial refunds | Omitting `refundAmount` requests a full refund; a positive value requests a partial refund. Windows, cumulative/concurrent refunds, identifiers, fees, processing time, finality, failures, chargebacks, and reversals are incomplete. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Split behavior | Create documents one `merchantId` or `+355` phone recipient; a merchant recipient must have an account in the order currency. Approval for RISHIT sellers, atomicity, timing, create/capture matching, recipient notification, commission use, insufficient balances, and multi-seller behavior are not defined. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Settlement and payout | No complete seller balance, settlement-eligibility, payout-rail/schedule, reserve, negative-balance, reversal, report, or delayed-release contract was found. Manual capture must not be described as escrow. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Webhook authentication and events | Create accepts `webhookUrl`, but no authoritative event catalogue, payload schema, signing/authentication, key rotation, replay protection, source-IP guidance, stable event ID, or retention/replay procedure was found. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Webhook retries and order | No retry schedule, timeout, acknowledgement rule, duplicate-delivery contract, ordering guarantee, or recovery procedure was found. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Stable IDs | Responses expose order and transaction identifiers, but uniqueness scope, stability, environment boundaries, event/refund/payout identifiers, retention, and reuse guarantees are not documented completely. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Idempotency | Client SDK text says a network retry against the same order ID is idempotent. The public server API documents no idempotency-key or duplicate-mutation contract for create, capture, cancel, or refund. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Retrieval | Known-order retrieval is documented and can return transaction/reference and payment-flow details. The complete status catalogue, freshness, retention, and authoritative recovery rules are not documented. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Reconciliation | No documented list/search/export endpoint, settlement/payout report, cutoff, report schema, retention, discrepancy procedure, or source-of-truth hierarchy was found. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Rate limits and operations | No limits/headers, backoff, request timeouts, availability target, maintenance notice, incident escalation, versioning, or deprecation contract was found. **REQUIRES WRITTEN POK CONFIRMATION.** |
| Staging access | The host, separate credentials, and test cards are documented. RISHIT credentials, EUR/split/seller entities, failure scenarios, reset policy, production parity, and support path are unavailable. **NO-GO FOR SPIKE.** |

## POK questionnaire — not sent

**Subject:** RISHIT Albania C2C marketplace — written approval and staging evidence

1. Identify the contracting and regulated entities and approve or reject in writing
   RISHIT's Albania-first C2C marketplace flow. Identify merchant of record, payee,
   payment-service user, seller recipient, funds owner, refund/chargeback owner, and
   every KYC/AML, tax/fiscal, safeguarding, dispute, and support responsibility.
2. Confirm EUR for the exact staging and production merchant profiles. For every
   order, product, shipping, authorization, capture, split, refund, fee, settlement,
   and payout field, define units, decimal precision, rounding, minimum, and maximum.
3. Define seller onboarding: POK user/currency/merchant account, hosted flow,
   required evidence, age/residency, Albania eligibility, KYC/KYB states, thresholds,
   refresh/review, rejection/restriction, and RISHIT-visible results.
4. Provide the complete payment state machine and stable identifiers. State
   uniqueness scope, lifetime, environment boundaries, retention, and retrieval for
   orders, authorizations, captures, cancellations, refunds, events, splits,
   settlements, payouts, chargebacks, and reversals.
5. For `autoCapture=false`, define authorization start/hold, validity/capture
   deadline, expiry, partial/multiple capture, cancel eligibility, finality,
   asynchronous failures, retry/recovery, and authoritative retrieval evidence.
6. Define full, partial, repeated, and concurrent refund rules: window, cumulative
   limit, allocation after split, identifiers, timing, fees, recipient debit,
   insufficient balance, chargeback/reversal interaction, failure, and finality.
7. Define `splitWith`: approved recipient types, seller onboarding, currency account,
   timing/atomicity, platform amount, recipient availability, delayed release,
   create/capture consistency, failures, fees, reports, reserves, negative balances,
   settlement, payout, and reversal. State explicitly whether any of this is escrow.
8. Supply the webhook contract: event names/versions, payloads, stable event IDs,
   signature/authentication and rotation, timestamp/replay rules, IP guidance,
   timeout/acknowledgement, retries, duplicates, ordering, retention/replay, and
   recovery when delivery fails.
9. Identify idempotency support for every mutation, duplicate semantics, conflict
   responses, retention window, and safe retry rules. Provide complete retrieval,
   search/list/export, settlement/payout reports, reconciliation cutoffs, status
   freshness, retention, discrepancy handling, and source-of-truth hierarchy.
10. Provide rate limits/headers, timeouts, backoff, availability/support targets,
    incident and maintenance contacts, API versioning, breaking-change, and
    deprecation policy.
11. Provision authorized staging-only RISHIT merchant and representative seller/user/
    merchant entities covering EUR, authorization/capture/cancel, full/partial/split
    refund, webhook duplicates/out-of-order delivery, failures, reconciliation, and
    payout. Confirm staging parity, reset rules, and provider-supported cases.
12. Provide the applicable commercial schedule and agreements: processing, refund,
    chargeback, split, FX, settlement, payout, reserve, negative-balance, support,
    termination, data roles/DPA, subprocessors/transfers, security incidents, audit,
    and record availability. Confirm Albania separately from future Kosovo scope.

## Platform-account bank-transfer findings

Albania has operational regulated EUR transfer infrastructure. That establishes
rail availability only. With no selected provider or account, every method-specific
question remains unanswered: recipient ownership, RISHIT's regulated role, unique
reference behavior, incoming-payment evidence, timing/finality, refunds, reversals,
fees, reconciliation, privacy roles, and marketplace legality. The method stays
disabled and **NO-GO**.

## Bank/provider questionnaire — not sent

1. Which legal entity owns the recipient EUR account/IBAN, is it dedicated to
   RISHIT, and may it collect buyer funds for third-party C2C sellers? Identify every
   regulated/contractual role, safeguarding treatment, beneficial owner, and
   prohibition or approval that applies to RISHIT's marketplace model.
2. Which incoming EUR rails are supported (domestic AIPS EURO, SEPA Credit Transfer,
   SEPA Instant, or other), from which payer locations/account types, with what
   availability, cutoffs, holidays, value dates, finality, and settlement timing?
3. Can RISHIT issue one immutable unique reference per order? Define allowed format,
   length, normalization, truncation, payer-bank alteration, reuse, collision handling,
   missing/wrong references, over/under/duplicate payments, and matching confidence.
   Is a virtual IBAN or equivalent available?
4. Provide authoritative incoming-payment retrieval and event contracts: authentication,
   fields, payer/payee/reference/amount/currency/value date, stable transaction/event
   IDs, pending/booked/rejected/reversed states, freshness, pagination, retention,
   webhooks/signatures, retries, duplicates, ordering, replay, and recovery.
5. Define return, recall, refund, rejection, reversal, and mistaken-payment procedures,
   including authority, deadlines, beneficiary checks, partial amounts, fees,
   identifiers, seller-settlement interaction, failures, and reconciliation evidence.
   A screenshot cannot be used.
6. Provide all account, incoming/outgoing transfer, refund/return, investigation,
   API, report, FX, support, and closure fees and say who invoices and bears each fee.
7. Provide intraday/end-of-day statements and API/export reports, formats, cutoffs,
   balances, value/booking dates, stable IDs, retention, correction files, discrepancy
   procedure, and support escalation needed for daily financial reconciliation.
8. Allocate controller/processor/joint-controller roles and purposes for buyer,
   seller, payer-account, transaction, KYC/AML, fraud, support, and reconciliation
   data. Supply the DPA, subprocessors, transfers, retention/deletion, access,
   incident, audit, and data-subject handling terms.
9. Provide written provider and qualified Albanian counsel confirmation that this
   account and flow are lawful for the proposed marketplace, including whether
   RISHIT needs licensing/registration/agency status and who performs KYC/AML,
   sanctions, monitoring, safeguarding, tax/fiscal reporting, complaints, refunds,
   and seller payout.
10. Provide authorized sandbox/staging access or a controlled test procedure covering
    success, delayed booking, wrong/missing reference, duplicate/partial/overpayment,
    return/reversal, refund, webhook failure/duplicate/out-of-order delivery,
    retrieval, statement export, and reconciliation, plus rate limits and support.

No bank-transfer method may proceed until one named provider answers this
questionnaire for one named account/product and counsel approves the resulting
responsibility matrix.
