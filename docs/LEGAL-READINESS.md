# Legal and Policy Readiness Checklist

This is a technical and product issue register prepared for review by qualified Albanian counsel. It is not legal advice, does not state what Albanian law requires, and is not a substitute for payment-provider, courier, tax, accounting, or regulatory advice.

Every item below is open unless a dated answer, approver, jurisdiction, policy version, and supporting evidence are checked into the project. Implementation or provider API behavior alone does not close a legal question. Kosovo requires a separate review before expansion.

## Confirmed product decisions

These decisions may be changed only through the authoritative decision register in [ROADMAP.md](ROADMAP.md). Counsel should assess the model built from them rather than treat them as legal conclusions.

- **C-01 — Product model:** RISHIT is designed as a transactional C2C marketplace for second-hand fashion, not an off-platform classifieds service. Counsel must still determine the platform's legal role and how professional or trader sellers are handled.
- **C-02 — Launch market:** Albania is the launch market. Kosovo is a later, separately gated expansion.
- **C-03 — Language:** public customer UI supports Albanian (`sq`) and English (`en`); user-authored content remains in its original language.
- **C-04 — Currency:** new public listings and marketplace transactions use EUR, represented internally as integer cents with explicit currency.
- **C-05 — Seller charges:** listing an item costs the seller EUR 0 and selling an item costs the seller EUR 0. No general "fee-free" claim is approved because buyer-side charges are unresolved.
- **C-06 — Buyer charges:** no buyer-side fee or Buyer Protection charge has been approved.
- **C-07 — Inventory:** a listing represents one unique physical item. Availability, purchase, bidding, and auction closure must be server-authoritative and concurrency-safe when those flows are implemented.
- **C-08 — Provider boundaries:** payments and shipping remain provider-neutral until actual providers and terms are approved. Delivery, order completion, settlement eligibility, and payout are distinct facts.
- **C-09 — Current capability:** no production order, payment, shipping, offer, auction, messaging, review, return, dispute, settlement, or payout flow is implemented or presented as complete.
- **C-10 — Original product:** Vinted is only a UX/product-flow reference. RISHIT uses original text, code, assets, photography, trademarks, and visual identity.

## Reversible engineering assumptions

These are safe implementation boundaries, not policy promises. Keep them configurable or replaceable until the corresponding review item is closed.

- **A-01 — Feature gates:** payment, payout, Buyer Protection, returns, disputes, auctions, and Kosovo launch stay disabled until their required approvals and provider evidence exist.
- **A-02 — Policy evidence:** terms, notices, fee rules, restricted-goods rules, and consent text will be versioned; acceptance or acknowledgement records will store the applicable version and timestamp where required.
- **A-03 — Separate roles and states:** platform, buyer, seller, payment provider, and courier roles remain explicit. Authorization, capture, refund, delivery, completion, settlement eligibility, and payout are not aliases.
- **A-04 — Data minimization:** where provider terms permit, RISHIT stores KYC status and provider references rather than identity-document images or raw provider payloads.
- **A-05 — Cookies:** non-essential cookies stay disabled until lawful-basis, consent, notice, and withdrawal requirements for each category are approved.
- **A-06 — Retention:** retention periods are policy-driven rather than scattered through code. Deletion, anonymization, legal hold, financial audit, fraud, and dispute records remain distinguishable.
- **A-07 — Rights operations:** export, correction, restriction, and deletion requests use authenticated, auditable workflows; the exact data scope, exceptions, format, and deadlines await counsel approval.
- **A-08 — Fees:** buyer-fee calculation and Buyer Protection wording remain absent, not silently treated as a permanent zero-fee policy. Any approved rule will be versioned and snapshotted on orders.
- **A-09 — Moderation:** restricted-goods rules, reports, decisions, appeals, and evidence are policy-driven and auditable rather than hardcoded into public copy.
- **A-10 — Dispute evidence:** messages, images, tracking, identity, and payment evidence use purpose-limited authorization and configurable retention.
- **A-11 — Auctions:** timing and increment rules remain configurable, while all bidding remains disabled until the auction policy and legal gate closes.
- **A-12 — Kosovo:** market, provider, courier, tax, terms, privacy, language, and moderation configuration are separate from Albania; Albania approval is never inherited automatically.

## Decisions requiring counsel or provider confirmation

### Marketplace contracting model and terms

- [ ] **L-01 (Albanian counsel):** identify the contracting parties for listing, fixed-price purchase, auction sale, payment, shipping, returns, and any platform service.
- [ ] **L-02 (Albanian counsel):** classify RISHIT's role in each flow, including whether it acts as intermediary, agent, marketplace operator, merchant, or in another capacity; approve matching public descriptions.
- [ ] **L-03 (Albanian counsel):** determine how a seller is classified as private, professional, or trader; define onboarding questions, evidence, reclassification triggers, and customer-facing labels.
- [ ] **L-04 (Albanian counsel):** approve the moment and evidence of contract formation for fixed-price purchases, accepted offers, Buy Now, and winning bids.
- [ ] **L-05 (Albanian counsel):** approve age/capacity rules, guardian consent if applicable, account suspension/termination rights, and consequences for existing transactions.
- [ ] **L-06 (Albanian counsel):** approve required pre-contract disclosures, terms acceptance mechanics, electronic records, policy-change notice, governing law, jurisdiction, complaint handling, and any mandatory ADR route.
- [ ] **L-07 (Albanian counsel):** review liability allocation, disclaimers, limitations, indemnities, service availability language, and which provisions cannot be limited by contract.
- [ ] **L-08 (Albanian counsel):** approve Albanian and English legal text and decide which version controls without implying that translation removes mandatory language obligations.

### Consumer rights and returns

- [ ] **L-09 (Albanian counsel):** determine which consumer rules apply to C2C sales, platform services, and sales by professional/trader sellers; define how mixed seller types must appear in the UI.
- [ ] **L-10 (Albanian counsel):** identify mandatory item, seller, price, fee, delivery, complaint, and cancellation information required before commitment.
- [ ] **L-11 (Albanian counsel):** determine withdrawal, conformity, defect, misdescription, counterfeit, non-delivery, and cancellation rights and any lawful exceptions for each seller type.
- [ ] **L-12 (Counsel + product):** approve return eligibility, request window, evidence/condition standard, approval authority, return deadline, inspection outcome, and rejection/appeal path.
- [ ] **L-13 (Counsel + payment/courier providers):** approve who pays original and return shipping, refund scope and timing, failed/refused/unclaimed parcel handling, partial refunds, and provider/chargeback interactions.
- [ ] **L-14 (Albanian counsel):** approve complaint response, escalation, recordkeeping, regulator/ADR notices, and any mandatory customer-remedy wording.

### Privacy, cookies, retention, export, and deletion

- [ ] **L-15 (Albanian privacy counsel):** map controller, joint-controller, and processor roles for account, listing, order, payment, KYC, courier, messaging, moderation, analytics, and support data.
- [ ] **L-16 (Albanian privacy counsel):** approve purposes and lawful bases per data category, including fraud prevention, financial audit, marketing, and any automated risk or moderation decisions.
- [ ] **L-17 (Counsel + engineering):** approve a data inventory and retention schedule for accounts, listings/images, favorites, orders, addresses, messages, payment metadata, KYC status/evidence, tracking, disputes, moderation, support, security logs, backups, and consent records.
- [ ] **L-18 (Albanian privacy counsel):** define access, correction, restriction, objection, portability/export, and deletion/anonymization scope, authentication, exceptions, response deadlines, and treatment of backups and legal holds.
- [ ] **L-19 (Albanian privacy counsel):** approve cookie categories, consent requirements, consent withdrawal, records, notice placement, and rules for analytics, advertising, personalization, and third-party embeds. Non-essential cookies remain off meanwhile.
- [ ] **L-20 (Counsel + providers):** approve privacy notices, recipient/subprocessor disclosures, international-transfer mechanisms, provider data terms, incident notification duties, and data-subject contact handling.
- [ ] **L-21 (Albanian privacy counsel):** determine children/minor rules, age assurance, profiling restrictions, DPO/representative requirements if any, and supervisory-authority contact wording.

### Seller identity and KYC responsibility

- [ ] **L-22 (Albanian counsel + payment provider):** determine whether and when RISHIT has KYC, AML, sanctions, beneficial-owner, or transaction-monitoring duties; do not infer them from a provider API.
- [ ] **L-23 (Payment provider):** document which users the provider onboards, required account type/currency, evidence collected, verification states, rejection/review handling, refresh frequency, and marketplace access to results.
- [ ] **L-24 (Counsel + provider):** allocate responsibility for seller, bidder, and payout-recipient verification, including thresholds, risk triggers, re-verification, failed verification, and account/transaction restrictions.
- [ ] **L-25 (Privacy counsel + provider):** approve notices, data sharing, controller/processor roles, access limits, retention, deletion restrictions, international transfers, and incident duties for identity data.
- [ ] **L-26 (Albanian counsel):** approve the meaning and evidence standard for any public "verified" badge; no identity, authenticity, safety, or trust guarantee may be implied.

### Payment, settlement, and payout wording

- [ ] **L-27 (Albanian counsel + payment provider):** identify merchant/recipient roles and confirm that the marketplace model, EUR flow, split/commission model, and seller onboarding are contractually permitted.
- [ ] **L-28 (Albanian counsel + provider):** determine who receives, controls, safeguards, holds, captures, refunds, reverses, and pays out funds, and whether any activity would require authorization RISHIT does not have.
- [ ] **L-29 (Payment provider):** provide authoritative terminology and lifecycle evidence for authorization, capture, cancellation, refund, chargeback, reserve, settlement, payout, reversal, negative balance, and reconciliation.
- [ ] **L-30 (Counsel + provider + product):** approve customer wording and timing for payment confirmation, seller settlement eligibility, expected payout, delays, failure, reversal, and support responsibility.
- [ ] **L-31 (Counsel + provider):** approve refund and chargeback allocation, split-transaction reversal behavior, insufficient-balance handling, records, notices, and who bears each loss.
- [ ] **L-32 (Counsel + provider):** confirm that delivery or a closed dispute window does not itself imply custody, settlement, payout, or guaranteed release of funds; approve the actual evidence gate.

### Buyer fee and Buyer Protection wording

- [ ] **L-33 (Product + finance + Albanian counsel):** decide whether a buyer-side fee exists. If yes, approve amount/basis, rounding, caps, VAT/tax, display, collection, snapshot/version, and change notice.
- [ ] **L-34 (Albanian counsel + provider):** define the exact service, eligibility, exclusions, evidence, deadlines, remedies, refundability, and responsible party behind any Buyer Protection proposition.
- [ ] **L-35 (Albanian counsel):** confirm how the service interacts with mandatory consumer rights, C2C remedies, returns, provider disputes/chargebacks, courier claims, and ADR; approve wording that does not purport to waive any applicable mandatory rights.
- [ ] **L-36 (Albanian counsel + provider):** approve the name and every public claim; do not describe the service as escrow, insurance, a guarantee, custody, or assured reimbursement without documented authority and capability.
- [ ] **L-37 (Product + counsel):** approve checkout, listing, SEO, help-centre, terms, refund, and invoice wording together so price and coverage claims do not conflict across surfaces.

### Tax, VAT, and fiscal responsibilities

- [ ] **L-38 (Qualified Albanian tax/fiscal adviser + counsel):** determine RISHIT's registration, VAT, invoicing, receipt, fiscalization, bookkeeping, and reporting duties for platform services, buyer fees, shipping charges, refunds, and provider/courier charges.
- [ ] **L-39 (Tax adviser + counsel):** determine private/professional seller tax classification, seller disclosure obligations, platform reporting or withholding duties if any, and what RISHIT may say about seller responsibilities.
- [ ] **L-40 (Tax adviser + provider/courier):** confirm invoice issuer, taxable amount, VAT treatment, tax point, credit/refund documents, fee netting, payout statement fields, and reconciliation evidence for each money flow.
- [ ] **L-41 (Tax adviser + engineering):** approve tax/fiscal record fields and retention without changing the EUR integer-cent invariant or rewriting historical snapshots.
- [ ] **L-42 (Albania and Kosovo advisers):** determine cross-border VAT/tax, customs, duties, declarations, seller/buyer obligations, and required checkout/shipping disclosures before Albania–Kosovo trade is enabled.

### Restricted goods and moderation

- [ ] **L-43 (Albanian counsel):** approve a restricted/prohibited-goods schedule and category-specific listing rules, including counterfeit/IP-infringing, stolen, recalled, unsafe, regulated, hygiene-sensitive, cosmetic, and age-restricted goods as applicable.
- [ ] **L-44 (Counsel + trust/safety):** define notice/report intake, evidence preservation, review authority, takedown/restriction, seller notice, appeal, repeat-offender, emergency, and regulator/law-enforcement procedures.
- [ ] **L-45 (Albanian counsel):** define platform knowledge, monitoring, response, reporting, and recordkeeping responsibilities; do not promise universal pre-screening or authenticity verification.
- [ ] **L-46 (Privacy counsel + trust/safety):** approve moderator access, sensitive allegation handling, reporter identity protection/disclosure, retention, deletion exceptions, and audit-log scope.
- [ ] **L-47 (Counsel + product):** approve listing declarations, authenticity language, condition/flaw disclosure, sanctions, and customer-facing reasons/codes without implying that moderation guarantees legality or safety.

### Shipping loss, damage, and disputes

- [ ] **L-48 (Albanian counsel + courier):** identify the shipping contracting party, label purchaser, shipper of record, authorized claimant, and when risk for loss/damage passes among seller, buyer, platform, and courier.
- [ ] **L-49 (Courier + counsel):** document coverage, exclusions, packaging duties, prohibited goods, proof of value/condition, claim deadlines, liability/insurance limits, investigation, compensation, and appeal/escalation.
- [ ] **L-50 (Counsel + courier + payment provider):** approve order, refund, seller-payable, and dispute outcomes for lost, damaged, delayed, misdelivered, refused, failed, unclaimed, returned, or fraud-suspected parcels.
- [ ] **L-51 (Counsel + product):** approve shipment deadline, delivery evidence, deemed-delivery rules if any, issue-report window, evidence access, decision authority, remedy matrix, and communications.
- [ ] **L-52 (Privacy counsel + courier):** approve address/phone sharing, tracking visibility, proof-of-delivery data, retention, subprocessors, international transfer, and incident duties.

### Auctions and bidding

- [ ] **L-53 (Albanian counsel):** determine whether the proposed online auction model triggers auction, licensing, consumer, gambling, competition, or other special rules.
- [ ] **L-54 (Albanian counsel):** approve bidder eligibility/verification, bid validity, contract-formation moment, withdrawal, cooling-off/returns interaction, and the legal effect of a winning bid or Buy Now.
- [ ] **L-55 (Counsel + product):** approve increment tiers, no-hidden-reserve/no-proxy rules, anti-sniping extension, seller cancellation, bid retraction, listing changes, winner payment deadline, failed-winner outcome, and relisting/next-bidder behavior.
- [ ] **L-56 (Albanian counsel):** define prohibited seller self-bidding, coordinated bidding, manipulation, employee/related-party participation, enforcement, notices, and appeals.
- [ ] **L-57 (Counsel + engineering):** approve required bid/terms/timestamp/audit evidence, retention, bidder privacy, price display, fee/protection disclosure, tax/fiscal records, and dispute handling.

### Kosovo expansion differences

- [ ] **K-01 (Qualified Kosovo counsel):** perform a separate contracting, marketplace-role, consumer, e-commerce, terms, jurisdiction, complaints, and ADR review; do not reuse Albania approval by default.
- [ ] **K-02 (Kosovo privacy counsel):** separately approve privacy roles, notices, lawful bases, cookies, rights handling, retention, transfers, breach duties, and regulator contacts.
- [ ] **K-03 (Kosovo counsel + payment provider):** confirm marketplace, EUR, merchant, KYC/AML, settlement, payout, refund, chargeback, and customer-wording support for Kosovo in writing.
- [ ] **K-04 (Kosovo tax/fiscal adviser):** approve VAT/tax, invoices/receipts, fiscalization, platform/seller reporting, withholding, customs, and Albania–Kosovo cross-border treatment.
- [ ] **K-05 (Kosovo counsel + courier):** approve coverage, contracting, prohibited goods, loss/damage liability, returns, cross-border/customs, personal-data sharing, and dispute routes.
- [ ] **K-06 (Kosovo counsel + product):** approve required language(s), disclosures, moderation rules, auction rules, public claims, and support/complaint capability before Kosovo appears as available.

## Roadmap impact: Tasks 08–17

This table uses the product task numbers in [ROADMAP.md](ROADMAP.md). `DONE` describes verified engineering scope, not production legal clearance.

| Task | Legal/policy impact | Gate |
| --- | --- | --- |
| 08 — Favorites | Existing private-feature scope stays complete. Account/favorites notice, retention, export, and deletion remain part of the launch privacy gate. | L-15–L-21 before production launch (Task 35); does not reopen Task 08. |
| 08A — Order and fee domain | Internal provider-neutral records implement approved zero-fee policy `buyer_fee_none_v1`; no checkout, public fee claim, payment, shipping, refund, or settlement behavior is exposed. | L-15–L-21 for snapshot retention/staff access and L-33–L-42 before checkout or public fee claims. |
| 09 — Read-only API | No direct commerce gate. Future authenticated/mobile data use must follow approved privacy and rights handling. | L-15–L-21 before authenticated expansion (Task 36). |
| 10 — SEO baseline | Existing pages avoid unsupported claims. New commerce, protection, seller-verification, auction, and Kosovo SEO copy cannot precede approval. | Public-claims gate below; later Task 34 remains blocked by `B-LEGAL`. |
| 11 — Image hardening | No direct legal blocker to technical image safety. Image/listing retention, prohibited goods, IP reports, and moderation rules remain later gates. | L-17–L-18 and L-43–L-47 before Tasks 28/35, not before Task 11. |
| 12 — Fixed-price policy gate | Direct legal blocker: contracting, consumer/returns, protection claims, and transaction windows must be approved. | `B-LEGAL` and `B-RETURNS`; L-01–L-14 and L-33–L-37. |
| 13 — Order/reservation core | Transitively blocked by Task 12. Snapshot content and retention must not invent legal requirements. | Task 12 first; L-04, L-17–L-18, and approved policy/version fields. |
| 14 — Totals/fee snapshots | Policy `buyer_fee_none_v1` is defined and snapshotted; Task 14 remains transitively blocked by Task 12 for the full checkout policy. | L-33–L-42 before checkout or public fee claims. |
| 15 — POK validation | Direct provider/legal blocker: marketplace approval, roles, KYC, custody, settlement, refund, and payout wording need written evidence. | `B-PAY` and `B-LEGAL`; L-22–L-32 plus [POK-MARKETPLACE-RESEARCH.md](POK-MARKETPLACE-RESEARCH.md). |
| 16 — Payment integration | Direct provider/legal blocker. An API shape is not authorization to operate the proposed marketplace money flow. | Tasks 14/15 plus `B-PAY` and `B-LEGAL`; L-27–L-32 and L-38–L-41. |
| 17 — Fixed-price checkout | Transitively blocked through Tasks 12, 14, and 16; checkout copy also needs approved price, rights, payment, and remedy disclosures. | `B-PAY` and upstream legal gates; L-06, L-10–L-14, L-30, and L-33–L-42. |

## Claims that must not appear publicly yet

Until the relevant checklist items are closed, public UI, metadata, structured data, marketing, support copy, terms drafts, emails, and API fields must not claim or imply:

- that RISHIT, POK, or another party provides **escrow**, holds money in trust, safeguards funds, or releases funds automatically after delivery;
- that **Buyer Protection** exists, what it costs, what it covers, or that reimbursement/refund is guaranteed;
- that a payment is final, a seller is entitled to settlement, or payout occurs within a stated time merely because an order is paid, shipped, delivered, or completed;
- that RISHIT is licensed, regulated, insured, a payment service, merchant of record, agent, custodian, arbitrator, or guarantor without approved evidence and wording;
- "no fees", "free transactions", or a zero buyer fee; only the narrower confirmed EUR 0 seller listing and selling fees may be stated;
- a return right, window, free return, refund deadline, cancellation right, or dispute outcome not backed by approved policy and provider operations;
- that sellers, bidders, identities, listings, brands, authenticity, condition, legality, or safety are verified or guaranteed;
- guaranteed delivery dates, tracked/insured shipping, loss/damage reimbursement, courier coverage, or platform responsibility before a selected courier contract and policy support them;
- that a bid is legally binding, a winner must pay, an auction sale is final, or a seller may cancel/retract without approved auction terms;
- that RISHIT handles taxes for users, that sales are tax-free, or that VAT/fiscal/invoice duties have a particular allocation;
- that Kosovo is supported for accounts, payments, payouts, shipping, returns, disputes, auctions, tax, privacy, or cross-border trade.

## Evidence required to close the legal gate

- [ ] Dated Albania issue memo from qualified counsel answering each applicable `L-*` item, with unresolved risks explicitly listed.
- [ ] Responsibility matrix for RISHIT, buyer, seller, payment provider, and courier across contract, data, money, shipping, returns, disputes, tax documents, and support.
- [ ] Approved Albanian and English terms/notices/customer copy, including a version, owner, effective date, and translation approval.
- [ ] Approved return/remedy/dispute matrix and restricted-goods/moderation policy that engineering can encode without interpreting legal prose.
- [ ] Approved data inventory, lawful-basis record, cookie register, retention schedule, rights procedure, provider/subprocessor list, and incident responsibilities.
- [ ] Written payment-provider and courier answers for all provider-owned items, with contracts or authoritative documents referenced.
- [ ] Qualified tax/fiscal sign-off on fees, invoices/receipts, VAT, fiscalization, seller/platform reporting, refunds, and retained records.
- [ ] Product and engineering map each approved rule to tests, policy versions, audit events, UI/API copy, and feature gates before changing a blocked roadmap task to `READY`.
- [ ] Separate Kosovo evidence set closes every applicable `K-*` item before Kosovo availability is enabled or advertised.
