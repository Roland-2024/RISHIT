# Shipping Provider Requirements

Use this checklist before selecting a courier. Do not code an adapter until required capabilities and commercial terms are verified.

## Must have

- Albania service coverage by region/postcode and documented exclusions
- Seller pickup and/or practical drop-off network
- Tracked parcel lifecycle with stable provider references
- API authentication, shipment creation, cancellation, and status retrieval
- Label or courier-instruction generation
- Quote/pricing inputs, surcharges, weight/dimension limits, and price validity
- Returns, failed delivery, refused delivery, address correction, and loss/damage process
- Idempotent create behavior or a reliable merchant reference search
- Test/sandbox process and production support/SLA
- Data protection, retention, webhook security, and incident contacts

## Strong preference

- Signed webhooks with stable event IDs and retry documentation
- Kosovo coverage and Albania–Kosovo cross-border/customs support
- Pickup scheduling, drop-off location discovery, proof of delivery, insurance tiers
- Reverse logistics and return-label API
- Reconciliation/export tooling and operational dashboard

## Commercial/legal questions

- Contracting entity, VAT/tax treatment, billing cycle, liability/insurance limits
- Who may buy the label and who is shipper of record
- Prohibited goods and fashion-specific restrictions
- Personal-data controller/processor roles
- COD is a launch candidate, not an approved method. A candidate must additionally verify C2C eligibility, EUR collection/pricing, API and reconciliation, cash remittance, failed/refused delivery, returns, liability, privacy, and support without undermining the transactional marketplace design.

## Selection output

Record verified capabilities, missing requirements, pricing examples in EUR, API documents, sandbox evidence, support contacts, and a go/no-go decision.

Research completed on 2026-08-11 is recorded in
[COURIER-RESEARCH.md](COURIER-RESEARCH.md). The public evidence does not verify
all mandatory requirements for any candidate. Final provider: **UNRESOLVED**.
Adapter implementation remains blocked pending written provider answers,
commercial terms, and sandbox evidence.

Counsel/provider decisions for contracting roles, loss/damage, returns, disputes,
privacy, tax, public wording, and Kosovo are tracked in
[LEGAL-READINESS.md](LEGAL-READINESS.md).
