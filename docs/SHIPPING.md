# Shipping

No courier has been selected and no production shipping integration exists.

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
