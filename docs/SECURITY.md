# Security

## Implemented baseline

- Laravel request-forgery/CSRF protection on web mutations
- Escaped Blade output and server-side validation
- Password hashing through Laravel’s hashed model cast
- Session regeneration after registration/login; invalidation/token regeneration on logout
- Throttled login and registration posts
- Unique username/email database constraints
- `.env`/credentials excluded from Git
- Authentication pages excluded from search indexing
- API exceptions rendered as JSON
- Owner-only listing policy for edit, visibility, and deletion
- Image type/size/count validation, hashed storage names, and failed-write orphan cleanup
- Hidden and soft-deleted listings excluded from public catalog/API queries
- Authenticated account settings with unique profile validation and no buyer/seller role split
- Owner-only address-book policies; private addresses are absent from public profiles and public APIs
- Albania-only address input with server-enforced recipient, `+355` phone, street, city, four-digit postal code, and fixed `AL` country code
- Transaction-locked unique-item order creation with server-derived totals and immutable commercial snapshots
- Participant/admin order policy plus append-only normalized state-transition history

## Required controls by domain

- Policies for offers, auctions, messages, disputes, and broader admin actions
- Image re-encoding and metadata stripping before production uploads
- Transactions, row locks, uniqueness, and retry handling for unique inventory and bids
- Verified webhook authentication, replay protection, stable provider event uniqueness, redacted logs, and safe retries
- Least-privilege database, Redis, object storage, deployment, and provider credentials
- Audit records for moderation and financial/admin transitions
- Rate limits for auth, messaging, offers, bids, checkout, and sensitive API routes

## Payment boundaries

Never store or log PAN, CVV, raw card input, POK secrets, or short-lived encrypted payloads beyond immediate exchange. Browser callbacks cannot mark an order paid. Payment, refund, payout, and settlement state requires authenticated provider evidence and reconciliation.

## Privacy and operations

Saved addresses and phone numbers are available to their authenticated owner for address-book management and can be snapshotted by the internal order action for the order parties. They are not exposed through seller profiles, listings, or `/api/v1`; no checkout or order route exists. Administrative order access is policy-protected, but no administrative interface exists. Public transaction flows still require approved notice, retention, provider, and legal rules.

Address-book rows are deleted when their owner deletes them, while immutable order snapshots remain attached to their order. Account export/deletion handling, transactional snapshot retention, staff procedures, and incident handling remain launch requirements and must follow the decisions tracked in `LEGAL-READINESS.md`. Production must also use HTTPS, secure cookies, secret rotation, backups/restore tests, monitoring, dependency scanning, and security headers.

Legal/privacy/KYC/tax requirements for Albania and Kosovo remain outside technical assumption and require qualified review. The open questions, evidence requirements, and reversible engineering boundaries are tracked in [LEGAL-READINESS.md](LEGAL-READINESS.md).
