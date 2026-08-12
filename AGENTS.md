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

## Product invariants

- Public customer UI supports Albanian (`sq`) and English (`en`). Preserve user-authored content in its original language.
- New public listings and marketplace transactions use EUR only. Represent EUR internally as integer cents with an explicit currency.
- Listing an item costs the seller EUR 0, selling an item costs the seller EUR 0, and approved buyer policy `buyer_fee_none_v1` costs the buyer EUR 0. A future buyer charge requires a new approved version. No Buyer Protection claim is approved.
- Do not claim escrow, Buyer Protection, settlement, payment, or courier behavior without matching provider, policy, and legal confirmation.
- Vinted is a UX and product-flow reference only. Never copy its proprietary text, code, assets, photography, trademarks, or trade dress.
- Checked-in documentation records current decisions and supersedes conflicting requirements in older attached planning prompts.

## Numbered roadmap tasks

- Use one Codex task/chat for each distinct numbered roadmap outcome. Keep the roadmap-controller chat for coordination rather than implementation.
- Before starting a numbered task, verify every dependency listed in `docs/ROADMAP.md` is merged into `master`. If the roadmap does not exist yet, create it only when the requested task explicitly requires it.
- If a missing dependency or unresolved business decision creates financial, security, legal, or irreversible architecture risk, stop and report the exact blocker instead of inventing an answer.
- Work sequentially in the local checkout by default. Use a separate worktree only for independent parallel work, and do not run conflicting fixed-port Docker environments simultaneously.
- Update the roadmap task to `REVIEW` when its pull request is ready. Mark it `DONE` only after the pull request is merged.

## Git delivery for numbered tasks

- Use a branch named `codex/rishit-<task-number>-<short-name>`.
- Commit and push completed, verified work and open a pull request targeting `master`.
- Do not merge the pull request automatically.

## Final task report

- Report completed behavior, important architecture decisions, significant files and database changes, tests and verification actually performed, assumptions, unresolved blockers, branch/commit/pull-request details, and the single recommended next ready task.
