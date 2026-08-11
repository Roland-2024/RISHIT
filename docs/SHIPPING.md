# Shipping

No courier has been selected and no production shipping integration exists.

## Provider decision

The 2026-08-11 Albania launch research produced a **NO-GO on provider
selection**. Albanian Courier is first to validate because of its domestic
network, e-commerce pickup, tracking/proof-of-delivery, failed-attempt terms,
and Kosovo operation. Posta Shqiptare is second because its 533-office network
could provide the most practical seller drop-off coverage. Neither has a
publicly verified production API, retry-safe create/recovery contract, sandbox,
complete event model, current EUR commercial terms, or complete data/SLA terms.

DHL Express and UPS have stronger public API evidence, but domestic
Albania-to-Albania marketplace service, distributed seller hand-off, and
commercial fit remain unverified. Ulysses remains a reserve local candidate.
These are validation priorities, not selected or integrated providers.

See [COURIER-RESEARCH.md](COURIER-RESEARCH.md) for the evidence comparison,
commercial examples, go/no-go blockers, sources, and provider questions.

## Planned boundary

The order domain will request a quote/selection and consume normalized shipment facts. A courier adapter will own provider requests, authentication, labels, external status mapping, webhooks/polling, and provider references.

Planned internal shipment data includes order/provider/reference, tracking number, addresses or immutable snapshots, selected service/method, integer EUR shipping amount, label reference, normalized status, and shipped/delivered/returned timestamps. Sensitive address access must be authorization-scoped.

Likely normalized states: pending, ready for pickup, picked up, in transit, out for delivery, delivered, delivery failed, returning, returned, cancelled. Only actual provider capabilities will be implemented.

## Reliability rules

- Quote/price/service are snapshotted at order creation.
- Create/cancel/event processing is idempotent.
- Duplicate/out-of-order events cannot regress valid terminal state.
- Client assertions never directly set authoritative delivery.
- A missed webhook must be recoverable through provider retrieval/synchronization.
- Shipping failures preserve payment/order audit history.

See [SHIPPING-PROVIDER-REQUIREMENTS.md](SHIPPING-PROVIDER-REQUIREMENTS.md).
