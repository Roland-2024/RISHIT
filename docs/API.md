# API

The future Flutter application and other clients use REST routes under `/api/v1`. The current read-only surface is:

```http
GET /api/v1/health
GET /api/v1/listings
GET /api/v1/listings/{slug}
```

```json
{
  "data": {
    "status": "ok",
    "api_version": "v1"
  }
}
```

Listing collections accept `q`, `category`, `brand`, `condition`, optional `currency=EUR`, `min_price`, `max_price`, and `sort` (`newest`, `price_asc`, `price_desc`). They return only active EUR listings with pagination `links` and `meta`. Amounts are integer cents with explicit currency. Category resources include stable `sq` and `en` labels while listing title and description remain the seller's original text. Any other currency is rejected. The framework renders exceptions as JSON for `/api/*`. Sanctum is installed and the user model supports API tokens, but public mobile login/token and mutation endpoints are not implemented until their threat model and client flow are defined.

## Conventions

- Success resources use a top-level `data` envelope; collections will add `links`/`meta` as needed.
- Validation/auth/domain errors use stable status codes and machine-readable fields once the first domain endpoint exists.
- Controllers validate/authorize and call shared application/domain logic; no rule is copied from Blade flows.
- Clients never submit authoritative prices, totals, fees, payment state, shipping state, or winning-bid state.
- Mutating money/inventory/external-provider endpoints will require idempotency semantics.
- Breaking changes require a new API version; additive fields do not.

Do not create empty order/payment/auction endpoints before the corresponding behavior exists.
