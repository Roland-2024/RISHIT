# RISHIT Engineering Instructions

- Inspect the repository, Git status, relevant docs, code, and tests before changing anything.
- Read the relevant files in `docs/`; keep them aligned with implemented behavior.
- Preserve the modular Laravel monolith and use current Laravel conventions. Prefer framework or platform features over packages and abstractions.
- Use the Ponytail skill for coding tasks when it is available: enforce YAGNI and choose the smallest solution that genuinely meets the requirement.
- Make the smallest coherent change. Do not modify unrelated work, add speculative scaffolding, or commit secrets or customer data.
- Keep business rules in reusable application/domain code, never in Blade or duplicated between web and `/api/v1` controllers.
- Render essential public marketplace and SEO content in Blade HTML; JavaScript is enhancement only.
- Treat all client money, prices, fees, payment states, shipment states, roles, and auction states as untrusted.
- Keep amounts as integer minor units with an explicit currency. Snapshot financial decisions and make external event handling idempotent and auditable.
- Keep payment-provider details behind a provider boundary once an actual integration exists. Never store card data or claim escrow/settlement behavior without provider and legal confirmation.
- Keep shipping provider-neutral until a courier is selected; never present a fake integration as production-ready.
- Protect unique-item purchases and bidding with database transactions, constraints, and row locks. Never implement auctions without concurrency tests.
- Reuse the same business logic for Blade and the future mobile API. Version public API routes under `/api/v1`.
- Add or update the smallest useful automated test for non-trivial behavior. Run relevant tests, full tests when practical, formatting, and frontend builds.
- Review the final Git diff and report only verified behavior, assumptions, and relevant open decisions.
