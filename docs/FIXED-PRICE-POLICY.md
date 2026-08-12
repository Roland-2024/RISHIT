# Fixed-Price Policy Decision Record

Status: approved product and engineering policy, 2026-08-12. This record defines
reversible internal defaults for future implementation. It does not enable
checkout, payments, bank transfer, Cash on Delivery (COD), shipping, returns,
disputes, settlement, or payouts.

## Commercial policy

- New marketplace orders use EUR integer cents with explicit currency.
- Seller listing and selling fees are EUR 0.
- Buyer policy `buyer_fee_none_v1` is EUR 0, with EUR 0 tax, display amount,
  and refundable amount. A later charge requires a new approved version and
  cannot rewrite historical orders.
- No Buyer Protection, escrow, custody, settlement, refund, return, delivery,
  or payout proposition is approved.

## Payment-method gates and reservation profiles

No profile below approves a provider or public launch. A method stays disabled
until its provider, legal, privacy, tax, support, and reconciliation gates are
closed.

| Candidate method | Reservation start | Reservation deadline before confirmed payment | Approval gate |
| --- | --- | --- | --- |
| Instant online payment | The local order and unique-item reservation commit atomically, before the buyer leaves for provider-hosted payment. Provider-session creation happens after that commit and must be idempotent. | 15 minutes from the committed reservation timestamp. | Task 15 must approve an EUR marketplace flow and Task 16 must implement authoritative retrieval/events. |
| Platform/provider-controlled bank transfer | The local order and reservation commit when RISHIT issues a unique order reference and authoritative payment instructions for an approved platform/provider-controlled account. | 24 hours from the committed reservation timestamp. A different rail may use a different approved, versioned profile when provider evidence shows that 24 hours is unsuitable. | The account owner, EUR rail, reference uniqueness, retrieval, reconciliation, refunds, privacy, and support must be verified. |
| Cash on Delivery | Only if later approved, the reservation starts when the server accepts an eligible COD order using a selected courier's authoritative service and price. | There is no prepayment timeout. The item stays unavailable until cancellation before handoff or an authoritative parcel outcome. Seller handoff is due five calendar days after COD acceptance. | COD remains a launch candidate only. Tasks 15, 18, and their implementation successors must verify C2C support, EUR pricing, API/reconciliation, cash collection, remittance, failed/refused delivery, returns, liability, privacy, and support. |

A direct buyer-to-seller bank transfer is prohibited. A screenshot, receipt
upload, buyer statement, seller statement, or support assertion is never proof
of payment. Only authenticated provider/bank retrieval, reconciliation data, or
verified provider events may confirm payment. Sellers must not be asked to
upload bank statements.

## Expiry, cleanup, retries, and late confirmation

- `reservation_expires_at` is persisted from the applicable versioned profile;
  browser countdowns are informational only.
- The scheduler scans due reservations every minute. Each order is locked and
  rechecked before an idempotent transition, so concurrent payment confirmation
  or repeated cleanup cannot release the same item twice.
- A failed cleanup run is retried by the next one-minute schedule. Three
  consecutive failed runs trigger an operational alert; recovery still uses the
  same idempotent command rather than a separate state-changing path.
- A transient provider-session failure keeps an online reservation until its
  deadline while idempotent recovery retries. A definitive failure cancels it
  immediately. A failed payment attempt may be retried only inside the existing
  reservation window; it never extends the deadline.
- Confirmation received after expiry never revives the order or reservation and
  never takes the item from a later buyer. It creates a reconciliation exception.
  Any void, reversal, or refund follows verified provider behavior; no customer
  timing or outcome is promised before that behavior is approved.

## Cancellation authority

`Admin` below means a least-privilege, reason-coded, audited operation, not a
direct database edit.

| State or boundary | Buyer | Seller | Admin | System |
| --- | --- | --- | --- | --- |
| `created` or `awaiting_payment` | May cancel. | Cannot cancel directly. The seller may report that the item is unavailable for audited admin resolution. | May cancel for a documented correction, unavailable item, abuse, or support reason. | May cancel a definitive setup/payment failure or expire at the deadline. |
| Confirmed payment, before courier handoff | May request cancellation, but cannot make it authoritative. | May report inability to ship, but cannot make cancellation authoritative. | May complete a cancellation only through the approved, verified payment void/refund workflow. | May act on verified provider outcomes; a timeout alone cannot fabricate a refund. |
| Approved COD, before courier handoff | May cancel. | Cannot cancel directly; may report inability to ship. | May cancel with an audited reason. | Cancels when the handoff deadline expires. |
| After courier handoff | Cannot cancel. | Cannot cancel. | No ordinary cancellation; only an approved failed/refused parcel, return, dispute, or provider-recovery workflow may resolve the order. | Applies verified courier/payment outcomes only. |
| `delivered` or `completed` | Cannot cancel. | Cannot cancel. | No ordinary cancellation. | No ordinary cancellation. |

Applicable mandatory cancellation or withdrawal rights remain counsel-owned and
can add rights before checkout launches. They cannot be inferred by Task 13.

## Order and listing outcomes

| Trigger | Order outcome | Listing outcome |
| --- | --- | --- |
| Reservation deadline passes without confirmed payment | Terminal `expired`, with an audited system transition. | Return to `active` only if it is otherwise eligible and the seller has not hidden or deleted it. |
| Definitive payment/setup failure before confirmation | Terminal `cancelled` with reason `payment_failed`. | Same conditional reactivation as expiry. |
| Buyer cancels before payment or COD handoff | Terminal `cancelled` with actor and reason. | Same conditional reactivation as expiry. |
| Admin cancels after the seller reports that the item is unavailable | Terminal `cancelled` with actor and reason. | `hidden`; never automatically republish an item the seller declared unavailable. |
| Admin cancels before payment | Terminal `cancelled` with actor and reason. | Explicit audited choice of conditional reactivation or `hidden`. |
| Cancellation after payment | Remains non-terminal until verified void/refund handling records the approved outcome. | Remains unavailable; no automatic reactivation. |
| Failed/refused parcel or return | Provider/counsel-owned resolution state; do not treat it as delivered, completed, or refunded. | Remains unavailable until authoritative return evidence exists and the seller explicitly republishes it. |

Task 13 may add only the reservation, `expired`, and pre-payment `cancelled`
behavior needed by this record. Payment, refund, parcel, and exceptional states
belong to their later roadmap tasks.

## Shipment handoff

For a future enabled method, the seller must hand the parcel to the selected
courier within five calendar days of authoritative payment confirmation, or of
COD acceptance when COD is approved. The exact timestamp is snapshotted on the
order in `Europe/Tirane`; a public holiday does not silently change it.

Before handoff, reminders are operational notifications and never authoritative
state. If the deadline passes, online or bank-funded orders enter the verified
cancellation/refund path and COD orders cancel. The listing remains `hidden`
until the seller confirms that the item is available. Seller sanctions, refund
timing, and compensation remain provider/counsel-owned.

## Returns, parcels, disputes, and payouts

The following remain blocked and must not be guessed: mandatory cancellation or
withdrawal rights; return eligibility, windows, evidence, condition, shipping
cost, and refund scope; failed, refused, unclaimed, lost, damaged, or returned
parcel responsibility; dispute authority and remedies; settlement eligibility;
and payout timing, reversals, or guarantees.

Prefer provider-hosted seller onboarding, KYC, and payout details. If RISHIT must
store payout coordinates under Task 23, it must first approve a minimal data
inventory and then encrypt, mask, authorize, retain, and audit that data. Seller
bank statements are not an onboarding or reconciliation mechanism.

## Approved customer wording

The following is the complete product-approved wording set. Transactional copy
may be used only when the underlying feature is implemented and its applicable
provider/legal disclosure gate is closed. Public marketing and SEO use requires
specific legal approval. No synonym may introduce protection, escrow, guaranteed
refund, delivery, settlement, or payout implications.

| Purpose | Albanian (`sq`) | English (`en`) |
| --- | --- | --- |
| Seller listing fee | `Tarifa e publikimit për shitësin: 0 €.` | `Seller listing fee: €0.` |
| Seller selling fee | `Tarifa e shitjes për shitësin: 0 €.` | `Seller selling fee: €0.` |
| Buyer fee | `Tarifa e blerësit: 0 €.` | `Buyer fee: €0.` |
| Protection boundary | `Kjo porosi nuk përfshin Mbrojtjen e Blerësit.` | `Buyer Protection is not included with this order.` |
| Reservation | `Artikulli është i rezervuar deri më {date/time}.` | `The item is reserved until {date/time}.` |
| Payment authority | `Pagesa konfirmohet vetëm pasi verifikohet nga ofruesi i pagesës.` | `Payment is confirmed only after verification by the payment provider.` |
| Bank-transfer safety | `Mos e paguani shitësin drejtpërdrejt. Përdorni vetëm udhëzimet e pagesës për këtë porosi.` | `Do not pay the seller directly. Use only the payment instructions for this order.` |
| Late confirmation | `Pagesa u identifikua pasi rezervimi skadoi. Artikulli nuk u ble; pagesa po verifikohet.` | `Payment was identified after the reservation expired. The item was not purchased; the payment is being reviewed.` |
| Seller handoff | `Shitësi duhet t'ia dorëzojë pakon korrierit të zgjedhur deri më {date/time}.` | `The seller must hand the parcel to the selected courier by {date/time}.` |
| Current availability | `Pagesa, transporti, kthimet dhe Mbrojtja e Blerësit nuk ofrohen aktualisht.` | `Payment, shipping, returns, and Buyer Protection are not currently offered.` |

Until checkout exists, public surfaces may use only the current-availability
sentence above for these disabled transaction capabilities. COD or bank transfer
must not appear as available merely because this record defines their gates.
