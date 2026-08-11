# Albania Courier Research

Research date: 2026-08-11. This record applies the mandatory checklist in
[SHIPPING-PROVIDER-REQUIREMENTS.md](SHIPPING-PROVIDER-REQUIREMENTS.md) to the
Albania launch. Provider pages and documents were reviewed without a merchant
account, signed commercial proposal, or sandbox credentials.

## Decision

**NO-GO on provider selection. Final provider: UNRESOLVED. Do not implement a
courier adapter.**

No candidate has all mandatory requirements verified. The public evidence
splits into two groups:

- Albanian Courier and Posta Shqiptare have the best evidenced domestic
  coverage and practical seller hand-off.
- DHL Express and UPS have mature public API capabilities, but the reviewed
  sources do not verify a commercially suitable Albania-to-Albania marketplace
  service with distributed C2C seller pickup/drop-off.

Albanian Courier is the first provider to validate commercially because it has
the strongest combined domestic, e-commerce, drop-off, pickup, proof-of-delivery,
and Kosovo evidence. Posta Shqiptare is the second provider to validate because
its 533-office network may offer the most practical seller drop-off coverage.
This order is a validation shortlist, not a recommendation or integration claim.

## Evidence standard

- **Verified** means a provider-owned source directly supports the capability.
- **Partial** means some relevant capability is public, but a mandatory part is
  missing, ambiguous, account-dependent, dated, or not verified for Albania.
- **Unknown** means no adequate public provider evidence was found. Unknown does
  not mean the provider cannot support the requirement.

No sales form, browser tracking page, global feature list, or third-party store
plugin is treated as proof of a production API contract.

## Mandatory comparison

| Requirement | Albanian Courier / United Transport | Posta Shqiptare | Ulysses Enterprises | DHL Express | UPS / United Transport |
| --- | --- | --- | --- | --- | --- |
| Albania coverage and exclusions | **Partial.** AC states all cities and major rural centres and lists about 100 UnionNet points, but no postcode-level exclusions or service matrix was found. | **Partial.** Universal service covers all Albania and the provider publishes a 533-office network; product-specific e-commerce exclusions are not published. | **Partial.** Ulysses states every city and village, but no postcode/exclusion matrix was found. | **Unknown for domestic launch.** DHL has an Albania operation; MyDHL is described for time-definite international shipments. Albania-to-Albania availability was not verified. | **Unknown for domestic launch.** UPS has an Albanian authorised contractor and two listed Tirana locations; nationwide domestic marketplace coverage was not verified. |
| Seller pickup or practical drop-off | **Partial.** About 100 points are advertised and the provider states parcels can be collected at customer premises twice daily under agreement. Arbitrary one-off seller-address pickup terms are unknown. | **Partial.** 533 offices provide strong drop-off potential; the e-commerce package includes address pickup. Whether a platform label can be dropped at every office is unknown. | **Partial.** Free address pickup and nine named office locations are advertised; seller-scale scheduling rules are unknown. | **Partial.** MyDHL can book pickups and the Albania site has a location finder, but domestic availability, seller eligibility, and practical network density are unknown. | **Partial.** UPS publishes pickup/drop-off workflows globally, but the Albania location page lists only two Tirana locations and domestic service was not verified. |
| API auth, shipment create, shipment cancel, status retrieval | **Unknown.** The site advertises an AC software platform and online integration, but no public API specification, authentication, create/cancel operations, or retrieval contract was found. | **Unknown.** Browser/app tracking exists, but no public merchant shipping API or cancellation contract was found. | **Unknown.** Online booking and tracking exist, but no public domestic API contract was found. | **Partial.** MyDHL documents Basic Auth, shipment creation, and tracking. It documents pickup cancellation, not shipment/label cancellation. | **Partial.** UPS publicly offers OAuth APIs for shipping, tracking, rating, pickup, returns, and voiding unused labels. Albania account enablement and the exact cancellation boundary were not tested. |
| Quotes, labels, limits, surcharges, validity, tracking | **Partial.** Browser tracking, barcoded forms, proof of delivery, and old public domestic tariffs exist. No programmatic quote/label contract, current EUR tariff, dimensional rules, surcharges, or quote validity was verified. | **Partial.** Public tracking and current linked ALL tariffs exist. No API quote/label flow, EUR contract, or price-validity field was found. | **Partial.** Real-time browser tracking and quote forms exist. No public price list suitable for domestic marketplace checkout or API labels was verified. | **Partial.** MyDHL documents rating, products, labels, tracking, dimensions, and account rates. Actual Albania products, EUR rates, surcharges, and domestic availability require an account. | **Partial.** UPS documents rating, labels, tracking, EUR international tariffs, dimensional weight, and surcharges. Domestic Albania rates and account enablement were not verified. |
| Webhooks or reliable polling | **Unknown.** Public tracking does not establish an authenticated polling API or event delivery contract. | **Unknown.** Real-time app/browser notifications do not establish merchant webhooks or an authenticated polling API. | **Unknown.** Browser tracking and status notifications do not establish a merchant event contract. | **Partial.** MyDHL supports tracking retrieval, including by shipment reference, so polling is technically documented. Poll limits, history completeness, and Albania product behaviour are untested; no signed webhook contract was found. | **Partial.** A Tracking API and sandbox tooling are public. Event history, polling limits, webhook security, and Albania enablement remain unverified. |
| Returns, failed/refused delivery, address correction, loss/damage | **Partial.** Terms state three delivery attempts for address/availability problems, sender liability for return/storage costs, declared-value insurance, liability limits, and claim windows. A return workflow/API, refused-delivery state model, address-correction rules, and charges are not published. | **Partial.** Public tariffs include non-delivery notice and return/address-change requests, and complaint windows are published. Refusal flow, reverse logistics, labels, exact compensation, and business SLA need confirmation. | **Partial.** Terms cover loss/damage/delay claims and declared value. Domestic refused delivery, retry count, reverse logistics, address correction, and charges are unknown. | **Partial.** On Demand Delivery supports receiver redelivery choices and MyDHL can create labels, but a confirmed Albania return product/API and failed/refused parcel rules were not found. | **Partial.** The 2026 Albania guide lists UPS Returns services, collection attempts, proof of delivery, declared value, and fees, subject to availability. Domestic applicability and API workflow were not tested. |
| Stable references and retry-safe creation | **Partial.** AC codes/barcodes and tracking are evidenced. Merchant-reference uniqueness/search and idempotent create semantics are unknown. | **Partial.** Registered-item tracking identifiers exist. Merchant reference, search, duplicate prevention, and idempotent create semantics are unknown. | **Partial.** Tracking identifiers exist. Merchant reference and idempotency semantics are unknown. | **Partial.** Customer references and tracking-by-reference are documented. Create idempotency, reference uniqueness, duplicate recovery, and retention windows are not. | **Partial.** UPS tracking and request transaction identifiers exist. They do not by themselves prove idempotent shipment creation or reliable merchant-reference recovery. |
| Sandbox, production support, SLA | **Unknown.** Public support contacts/hours exist; no sandbox, onboarding process, production SLA, severity matrix, or incident channel was found. | **Unknown.** Customer care and complaint processes exist; no merchant API sandbox or production integration SLA was found. | **Unknown.** Support contacts/hours exist; no API sandbox or production SLA was found. | **Partial.** MyDHL documents a test environment, 500 daily test calls, formal test-environment SLAs, account onboarding, and API support. Production SLA and local operational escalation require contract review. | **Partial.** The developer portal provides OAuth onboarding and sandbox requests. Local production support exists, but contracted SLA and Albania API enablement need confirmation. |
| Data protection, retention, event security, incident contacts | **Unknown.** Public carriage terms were found, but no adequate DPA, controller/processor allocation, retention schedule, API/webhook security, subprocessor list, or privacy incident contact was found. | **Partial.** Privacy and data-security statements are public, but reviewed text cites an older Albanian law and does not close retention, processor roles, event security, subprocessors, or incident escalation. | **Unknown.** No adequate domestic-service DPA, retention, event-security, or privacy incident record was found. | **Partial.** MyDHL terms allocate some data-law responsibilities and link a privacy notice. DPA, retention, subprocessors/transfers, webhook security, and incident contacts remain contractual questions. | **Partial.** Global developer/legal material exists, but Albania-specific DPA, retention, webhook/event security, and incident escalation were not verified. |
| EUR commercial terms and support | **Partial.** Albania domestic prices are publicly posted in ALL in a PDF uploaded in 2022. Kosovo prices are in EUR, including Kosovo-to-Albania, but they are not an Albania merchant quote. Current EUR billing, VAT treatment, discounts, surcharges, reconciliation, and SLA are unknown. | **Partial.** Public domestic tariffs are in ALL. No EUR invoice/contract, marketplace rate card, billing cycle, surcharge treatment, or price-lock terms were found. | **Unknown.** A quote form is public, but no domestic EUR marketplace rate card or commercial contract was found. | **Partial.** The API returns account rates and Albania business support is available. No actual Albania EUR quote, minimum volume, billing cycle, or domestic rate was obtained. | **Partial.** A 2026 Albania international tariff in EUR and local support are public. Domestic marketplace rates, negotiated terms, billing, and minimums are unknown. |

## Strong-preference comparison

| Requirement | Evidence and gap |
| --- | --- |
| Kosovo and cross-border | Albanian Courier calls itself a regional Albania/Kosovo courier, has a Kosovo operation, and publishes Kosovo-to-Albania rates. UPS publishes a 2026 Kosovo guide where Albania is a supported zone. Ulysses states it serves countries in the region and offers customs services. DHL is positioned for international express. None of the reviewed sources closes the full Albania-Kosovo customs, duties/taxes, return, prohibited-goods, data-transfer, or shipper-of-record workflow for RISHIT. |
| Pickup scheduling and location discovery | AC, Posta Shqiptare, and Ulysses have practical public pickup/drop-off evidence. DHL and UPS have technical pickup/location tooling. Exact per-seller scheduling, cutoff, cancellation, no-show, and rural surcharge rules remain unknown. |
| Proof of delivery and insurance | AC publishes proof-of-delivery and declared-value terms. DHL MyDHL includes electronic proof of delivery for eligible shipments and optional insurance. UPS publishes POD and declared-value services. Posta and Ulysses have partial claim/insured-item evidence. Availability, evidentiary format, retention, API retrieval, and limits require contracts. |
| Reverse logistics | UPS publishes the clearest return-product information. Posta and AC publish manual return-related rules. No candidate has a verified Albania marketplace return-label API plus commercial workflow. |
| Reconciliation/dashboard | AC, Posta, and Ulysses offer customer-facing systems, and DHL/UPS offer business tooling. Shipment-cost export, invoice reconciliation, adjustments, dispute exports, retention, and API access were not verified for any candidate. |

## Commercial evidence snapshot

These are evidence examples, not RISHIT checkout prices and not approved quotes.
No currency conversion is applied.

- **Posta Shqiptare:** the tariff file currently linked from its tariff page lists
  an ordinary domestic parcel at ALL 90 for 1-3 kg, an ALL 100 express add-on,
  an ALL 60 return/address-change request, and ALL 150 pickup at the sender's
  address within a city. The PDF has no visible effective/expiry date; a separate
  notice says revised tariffs took effect on 2026-05-04. Written confirmation is
  required before relying on the figures.
- **Albanian Courier Albania:** its public e-commerce PDF was uploaded in 2022
  and lists domestic ALL rates by weight and point/address routing. It is useful
  only as evidence of the pricing shape, not current pricing or price validity.
- **Albanian Courier Kosovo:** the public Kosovo page lists EUR 2.50 for 0-2 kg
  within a Kosovo city, EUR 3.50 between Kosovo cities, and EUR 10 for 0-1 kg
  from Kosovo to Albania. The page does not establish an Albania merchant
  contract, taxes, surcharges, customs treatment, or validity period.
- **UPS Albania:** the 2026 international guide is denominated in EUR, excludes
  taxes, and lists Returns Plus fees of EUR 6.30 for one collection attempt and
  EUR 8.40 for three attempts, in addition to transport. The guide says service
  availability conditions apply and does not provide a domestic Albania
  marketplace rate.
- **DHL Express and Ulysses:** no public domestic Albania EUR example suitable
  for a marketplace checkout was verified. Both require a commercial quote.

RISHIT must store only the provider's contracted EUR amount in integer cents.
Public ALL figures must not be relabelled, converted, or displayed as an approved
EUR shipping price.

## Shortlist and go/no-go blockers

### 1. Albanian Courier - validate first

Operational evidence is strongest: about 100 Albanian hand-off points, scheduled
business pickup, e-commerce service, barcode tracking, proof of delivery, three
delivery attempts, claims/insurance terms, and an associated Kosovo operation.

**NO-GO blockers:** production API specification and credentials; create and
shipment-cancel semantics; server-side quotes and label/instruction output;
authenticated status retrieval or webhooks; retry/idempotency and merchant
reference recovery; sandbox; current contracted EUR prices and surcharge
validity; returns/refusal/address-correction state contract; DPA/retention/event
security/incident channel; SLA; and one contract covering Albania/Kosovo roles.

### 2. Posta Shqiptare - validate second

Its 533-office network, e-commerce address pickup, nationwide public-service
coverage, tracking, published tariffs, and manual return/address-change services
make it the strongest drop-off alternative.

**NO-GO blockers:** merchant shipping API and sandbox; programmatic quote,
create/cancel, label, tracking history, events, and merchant references;
idempotency; every-office acceptance of platform parcels; current EUR commercial
terms; e-commerce return/refusal/loss workflow; production SLA; and complete
data-processing/security terms.

### 3. Ulysses Enterprises - reserve local candidate

Ulysses advertises no-cost address pickup, every-city/village delivery, real-time
tracking, multiple offices, regional service, customs capabilities, and a
FedEx/TNT relationship.

**NO-GO blockers:** almost the complete technical checklist for its own domestic
service, current EUR rate card, domestic returns/failed-delivery contract,
sandbox/SLA, data terms, and clarity on which capabilities belong to Ulysses
versus FedEx/TNT.

### 4. DHL Express and UPS - cross-border/API fallback only

Both retain value for a later cross-border lane or if a local commercial proposal
proves domestic marketplace suitability. DHL has the clearest tested create,
rate, label, pickup, and tracking API evidence. UPS has strong return-service and
current EUR international tariff evidence and is represented locally by United
Transport.

**NO-GO blockers:** Albania-to-Albania service and prices; practical arbitrary
seller pickup/drop-off; marketplace account and shipper-of-record eligibility;
shipment cancellation boundary; retry-safe creation and reference recovery;
event/reconciliation details; domestic returns; DPA/SLA; and live sandbox proof
for the contracted Albanian products.

## Questions for provider outreach

Ask every shortlisted provider to answer in writing and attach the referenced
contract, API page, rate card, or sandbox evidence.

1. Which legal entity contracts with RISHIT, who buys each label, and who is the
   shipper of record when the physical sender is an individual marketplace
   seller? Can pickup and return addresses vary per shipment?
2. Provide Albania service coverage by postcode/municipality, exclusions, rural
   frequency, pickup cutoffs, drop-off locations, and all remote-area rules.
3. Provide production and test API documentation for authentication, quote,
   shipment creation, label or courier instructions, shipment cancellation/void,
   pickup create/change/cancel, status history, proof of delivery, returns, and
   address correction.
4. What exact idempotency key or unique merchant-reference rule prevents a
   duplicate label on timeout/retry? Can RISHIT retrieve a shipment by merchant
   reference after an ambiguous response? State uniqueness scope and retention.
5. Are events pushed? If yes, document signature verification, key rotation,
   stable event IDs, retries, ordering, replay, IP requirements, and history. If
   polling is required, document rate limits, complete history, and retention.
6. Provide sandbox credentials and test cases for success, duplicate create,
   invalid address, quote expiry, cancel before/after pickup, failed/refused
   delivery, return, loss/damage, delayed/out-of-order events, and API outage.
7. Document attempts, recipient-unavailable and refused-delivery handling,
   address correction, storage, return-to-sender, reverse pickup/label, loss,
   damage, claims, proof, deadlines, liability, insurance, and every charge.
8. Quote representative 0.5 kg, 1 kg, 2 kg, and 5 kg parcels in EUR for Tirana,
   intercity, and rural lanes. State VAT/tax, fuel/remote/oversize/return/no-show
   surcharges, dimensional formula, prohibited goods, price validity, volume
   tiers, billing cycle, reconciliation exports, and payment terms.
9. Provide the DPA and state controller/processor roles, purposes, fields,
   retention/deletion, hosting, subprocessors, cross-border transfers, access
   controls, encryption, audit logs, breach notification time, and privacy and
   security incident contacts.
10. Provide onboarding time, production SLA, maintenance notice, rate limits,
    severity targets, support hours/languages, named technical and operational
    escalation paths, and service-credit terms.
11. For Kosovo, identify the operating entities and handoff, supported lanes,
    customs declarations, duties/taxes, shipper/exporter/importer of record,
    prohibited goods, return path, EUR prices, liability, and data transfers.
12. Confirm COD is optional and can be disabled. RISHIT payment authority and
    marketplace state cannot depend on a courier COD event.

Provider-specific follow-ups:

- **Albanian Courier:** provide the documentation behind "Integrate with the AC
  platform" and explain the API used by existing store integrations. Confirm
  whether AC Albania and AC Kosovo can be covered by one technical/commercial
  agreement.
- **Posta Shqiptare:** confirm whether contracted marketplace parcels can be
  prepaid in EUR and dropped at any of the 533 offices, and whether the business
  e-commerce service exposes an API distinct from e-Posta/browser tracking.
- **Ulysses:** separate the Ulysses domestic API, terms, labels, tracking, and
  support from FedEx/TNT international capabilities.
- **DHL Express:** confirm whether domestic Albania-to-Albania products exist,
  whether a created waybill can be voided through API, and which return product
  and arbitrary-seller pickup model are available in Albania.
- **UPS / United Transport:** confirm whether domestic Albanian Courier services
  are exposed through UPS APIs or require a separate AC contract and platform.

## Public outreach contacts

These channels are suitable for requesting the written evidence above. They are
not contracted incident or production-support contacts.

| Provider | Public channel |
| --- | --- |
| Albanian Courier Albania | +355 44 80 80 80; WhatsApp +355 67 200 0660; `info@albaniancourier.al` |
| Albanian Courier Kosovo | +383 49 161 313; `ac.kosova@albaniancourier.al` |
| Posta Shqiptare / e-Posta | 0800 4141; +355 68 204 4727; +355 68 204 4707; `asistence@postashqiptare.al` |
| Ulysses Enterprises | +355 42 253 203; `info@ulysses.al` |
| DHL Express Albania | Use the official Albania business-account, shipping-support, and MyDHL API access routes linked below; no named integration or incident contact was verified. |
| UPS Albania / United Transport | +355 44 80 80 80; use the official UPS Albania support route linked below. |

## Primary sources

Accessed 2026-08-11 unless otherwise noted.

### Albanian Courier / United Transport

- [Albania home, network, pickup, tracking, proof of delivery, and support](https://al.albaniancourier.al/)
- [Domestic e-commerce service](https://al.albaniancourier.al/sherbimi-postar-ekspress-per-blerje-shitje-online/)
- [Terms, attempts, returns costs, liability, and insurance](https://al.albaniancourier.al/terma-dhe-kushte/)
- [Claims procedure](https://al.albaniancourier.al/procedure/)
- [Albania tariff page and dated domestic files](https://al.albaniancourier.al/tarifat-e-sherbimit/)
- [Kosovo services, domestic and Albania rates](https://www.albaniancourier.al/ks/?p=3612)
- [Kosovo contact](https://www.albaniancourier.al/ks/?page_id=135)

### Posta Shqiptare

- [E-commerce package](https://www.postashqiptare.al/c/161/paketa-e-sherbimeve-e-commerce)
- [Universal service](https://www.postashqiptare.al/c/37/sherbim-postar-universal)
- [533-office network evidence](https://www.postashqiptare.al/c/181/te-dhenat-e-hapura-open-data)
- [Postal tariffs and linked current files](https://www.postashqiptare.al/c/42/tarifa-e-sherbimeve-postare)
- [2026 tariff revision notice](https://www.postashqiptare.al/d/539/mbi-rishikimin-e-tarifave-t-shrbimeve-postare)
- [Complaints and parcel inquiry windows](https://www.postashqiptare.al/c/47/ankimim)
- [Privacy policy](https://eposta.postashqiptare.al/privacy-policy)
- [Prohibited goods](https://www.postashqiptare.al/c/44/mallra-te-ndaluara)

### Ulysses Enterprises

- [Domestic coverage, pickup, tracking, and support](https://ulysses.al/en/services/1619/)
- [Locations and FedEx/TNT relationship](https://ulysses.al/en/ulysses-services/)
- [Terms and claims](https://ulysses.al/termat-dhe-kushtet/)

### DHL Express

- [MyDHL API reference, environments, operations, references, and terms](https://developer.dhl.com/api-reference/dhl-express-mydhl-api?lang=en)
- [DHL Express Albania](https://www.dhl.com/al-en/home/express.html)

### UPS

- [UPS developer onboarding and sandbox](https://developer.ups.com/get-started)
- [UPS Albania contact and authorised contractor](https://www.ups.com/al/en/support/contact-us)
- [UPS Albania locations](https://www.ups.com/al/en/locations)
- [UPS Albania rates page](https://www.ups.com/al/en/support/shipping-support/shipping-costs-rates)
- [2026 UPS Albania service and rates guide](https://al.albaniancourier.al/wp-content/uploads/sites/2/2026/01/2026_tariff-guide-al-gb-en.pdf)
- [2026 UPS Kosovo service and rates guide](https://assets.ups.com/adobe/assets/urn%3Aaaid%3Aaem%3Ad76a59a8-86d1-4c48-8d97-8bec753798d4/original/as/tariff-guide-xk-gb-en.pdf)

## Secondary clue not accepted as verification

- A [CloudCart help page](https://help.cloudcart.com/en/support/solutions/articles/77000537273-activation-and-settings-of-albanian-courier)
  describes an Albanian Courier store integration. It supports asking AC for its
  private integration documentation, but does not verify the mandatory API,
  security, idempotency, sandbox, or commercial requirements.
