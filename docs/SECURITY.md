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

## Required controls by domain

- Policies for offers, orders, auctions, messages, disputes, and admin actions
- Image re-encoding and metadata stripping before production uploads
- Transactions, row locks, uniqueness, and retry handling for unique inventory and bids
- Verified webhook authentication, replay protection, stable provider event uniqueness, redacted logs, and safe retries
- Least-privilege database, Redis, object storage, deployment, and provider credentials
- Audit records for moderation and financial/admin transitions
- Rate limits for auth, messaging, offers, bids, checkout, and sensitive API routes

## Payment boundaries

Never store or log PAN, CVV, raw card input, POK secrets, or short-lived encrypted payloads beyond immediate exchange. Browser callbacks cannot mark an order paid. Payment, refund, payout, and settlement state requires authenticated provider evidence and reconciliation.

## Privacy and operations

Addresses, phone numbers, messages, identity/KYC and dispute evidence need purpose-limited access, retention rules, export/deletion handling, and incident procedures before launch. Production must use HTTPS, secure cookies, secret rotation, backups/restore tests, monitoring, dependency scanning, and security headers.

Legal/privacy/KYC/tax requirements for Albania and Kosovo remain outside technical assumption and require qualified review.
