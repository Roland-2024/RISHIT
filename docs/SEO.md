# SEO

## Foundation implemented

- Public homepage is Blade-rendered under `/sq` and `/en`; `/` redirects to Albanian.
- Initial HTML contains meaningful headings, product proposition, internal links, title, description, canonical, Open Graph basics, and route-equivalent `sq`/`en` hreflang.
- Authentication pages are `noindex, nofollow`.
- JavaScript is not needed to read the public content.
- Home, catalog, listing, and seller pages expose useful server-rendered content without JavaScript.
- Search and facet URLs are useful to people but emit `noindex`; the base catalog remains indexable.
- Database-driven category and brand landing pages expose active EUR inventory with localized canonical and reciprocal hreflang metadata.

## Planned indexable pages

Listing, seller, category, and brand pages are implemented. Curated auction and editorial/ending-soon landing pages remain planned. Product HTML contains title, description, price/currency, brand, category, condition, size, seller, availability context, and important links. Public listing routes return `404` for hidden, sold, deleted, and legacy non-EUR inventory; authorized owners receive a server-rendered unavailable explanation instead.

## URL and localization rules

- Locale is a path prefix and the canonical URL includes it.
- Localized slugs may differ by language; a stable entity maps both routes.
- User listing text keeps its original language; future translations are separate and attributed.
- Every equivalent page emits self-canonical plus correct reciprocal hreflang.

## Facets

Arbitrary brand/size/color/condition/price/sort combinations will not be indexable. Default rule: filtered URLs are crawlable for users but `noindex` and canonical to the base page unless a curated landing page has sufficient inventory and unique content. Search/result parameters must not create unbounded crawl space.

## Phase 2/9 work

Product/Offer JSON-LD, BreadcrumbList, sitemap index and segmented sitemaps, robots policy, pagination/canonical validation, sold/removed item rules, Open Graph images, internal-link strategy, Core Web Vitals and crawler tests.
