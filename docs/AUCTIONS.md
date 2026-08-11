# Auctions

Auctions are RISHIT’s primary marketplace differentiator, but implementation waits until the fixed-price commerce path is production-quality.

## First auction MVP

- Seller chooses a EUR starting bid, duration (24h/72h/168h), and optional EUR Buy Now price.
- No hidden reserve and no proxy bidding.
- Starting bid is the seller’s minimum acceptable auction price.
- Bidder must be eligible, cannot be the seller, and must meet the current required amount/increment.
- Backend time/state is authoritative; no bid is accepted after closure.
- A valid bid in the final 120 seconds extends the end time by 120 seconds.
- Optional Buy Now atomically closes bidding and enters the normal commerce checkout path.

## Concurrency design

Bid placement will lock the auction row inside a database transaction, re-read authoritative state, validate the next price, insert the bid, update highest-bid state, and commit. Auction closure and Buy Now must lock the same state. Unique/idempotency constraints will protect retry paths.

Tests must include simultaneous bids, seller self-bids, late bids, invalid increments, extension boundaries, closure races, and Buy Now races. Reverb may broadcast committed changes, but clients always recover from database state.

## Unresolved before implementation

Increment tiers, bidder verification, winner payment deadline, failed-winner policy, cancellation rules, and legal auction requirements. The required Albania and Kosovo review questions are tracked in [LEGAL-READINESS.md](LEGAL-READINESS.md).
