# Product

RISHIT is a transactional C2C marketplace for second-hand fashion. Albania is the launch market, Kosovo is planned next, and further Balkan/European expansion remains optional.

## Product promise

RISHIT keeps discovery, payment, shipping, delivery, settlement eligibility, reviews, and disputes inside one accountable marketplace journey. It is not a classifieds site for exchanging phone numbers and arranging private payment.

Sellers pay €0 to list and €0 when an item sells. Approved buyer fee policy `buyer_fee_none_v1` is also €0, with no tax and a €0 refund; any future buyer-side charge requires a separately approved version shown before purchase.

No Buyer Protection service or claim is approved. Public marketing of the zero
buyer fee still requires applicable legal approval; the approved internal fee
policy may be stated accurately in product and engineering records.

The reliable loop is:

1. A seller lists one physical item.
2. A buyer discovers and purchases it.
3. The backend confirms availability and authoritative totals.
4. An approved provider confirms payment through authoritative evidence.
5. The seller follows tracked shipping instructions.
6. Delivery and any dispute window complete.
7. Seller settlement occurs only under confirmed provider/legal rules.

## Experience

Vinted is the product benchmark for familiar marketplace information architecture and transaction UX; RISHIT uses original branding, design, content, assets, and implementation. Auctions add eBay-style competitive selling with backend-controlled bidding, anti-sniping, and optional Buy Now.

## Markets and presentation

- Customer UI: Albanian (`sq`) and English (`en`)
- Listing prices and all future transactions: EUR only
- Language changes content, never the marketplace currency
- User-generated listing content always preserves its original text and language

These checked-in decisions supersede older planning-prompt requirements for ALL. ALL remains historical/legacy data context only: preserve existing records, keep them out of public discovery, and do not relabel or convert them without an approved migration policy.

## Current implementation

Phases 1 and 2 provide the original public shell, localization, authentication, catalog discovery, seller profiles, favorites, listing/photo management, Docker services, and read-only listing APIs. A provider-neutral internal order/fee foundation is also present, but no checkout, payment, shipping, offer, auction, messaging, or review flow is represented as complete.

Future fixed-price behavior is governed by [the approved policy record](FIXED-PRICE-POLICY.md). Instant online payment is the primary candidate. Platform-account bank transfer and COD are disabled candidates with separate evidence gates; direct buyer-to-seller bank transfer is not part of the product.
