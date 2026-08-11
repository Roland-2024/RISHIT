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
- Image type/size/count/dimension validation; GD decoding and safe re-encoding remove metadata and normalize EXIF orientation
- Random storage names, deterministic primary/display order, and cleanup after failed writes or image/listing deletion
- Hidden and soft-deleted listings excluded from public catalog/API queries
- Authenticated account settings with unique profile validation and no buyer/seller role split
- Owner-only address-book policies; private addresses are absent from public profiles and public APIs
- Albania-only address input with server-enforced recipient, `+355` phone, street, city, four-digit postal code, and fixed `AL` country code

## Required controls by domain

- Policies for offers, orders, auctions, messages, disputes, and admin actions
- Transactions, row locks, uniqueness, and retry handling for unique inventory and bids
- Verified webhook authentication, replay protection, stable provider event uniqueness, redacted logs, and safe retries
- Least-privilege database, Redis, object storage, deployment, and provider credentials
- Audit records for moderation and financial/admin transitions
- Rate limits for auth, messaging, offers, bids, checkout, and sensitive API routes

## Payment boundaries

Never store or log PAN, CVV, raw card input, POK secrets, or short-lived encrypted payloads beyond immediate exchange. Browser callbacks cannot mark an order paid. Payment, refund, payout, and settlement state requires authenticated provider evidence and reconciliation.

## Privacy and operations

Saved addresses and phone numbers are currently available only to their authenticated owner for address-book management. They are not exposed through seller profiles, listings, or `/api/v1`. A future checkout or shipping workflow may read and snapshot only the fields required for fulfillment after its provider, notice, retention, and legal rules are approved; support or administrative access is not implemented.

Addresses are deleted when their owner deletes them and cascade with an account record. Account export/deletion handling, transactional snapshot retention, staff access/audit, and incident procedures remain launch requirements and must follow the decisions tracked in `LEGAL-READINESS.md`. Production must also use HTTPS, secure cookies, secret rotation, backups/restore tests, monitoring, dependency scanning, and security headers.

Legal/privacy/KYC/tax requirements for Albania and Kosovo remain outside technical assumption and require qualified review. The open questions, evidence requirements, and reversible engineering boundaries are tracked in [LEGAL-READINESS.md](LEGAL-READINESS.md).
