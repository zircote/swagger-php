# Documentation Plan for Spec-Attributes Pipeline

## Context

The spec-attributes pipeline is feature-complete (beta). The existing docs were written for the classic pipeline only. We need enough documentation for users to discover, understand, and use the new pipeline modes — while keeping it clear that spec/hybrid are opt-in and beta.

The auto-generated reference pages (`reference/spec-attributes.md`, `reference/augmenters.md`) are already done. This plan covers the remaining hand-written documentation.

**Note:** `docs/spec-attributes.md` is a temporary tracking document that will be deleted once the pipeline ships. Any content worth keeping must land in proper docs pages as part of this work.

## Pages to create or update

### 1. Update `reference/builder.md`

The current page only documents classic mode. Needs:

- Document all three modes (`classic`, `spec`, `hybrid`) with one-paragraph explanation each
- Show `setMode()` with valid values
- Add `withAugmenters()` API (analogous to `withGenerator()` for classic)
- Mention version resolution for spec/hybrid (setVersion > source attribute > fallback)
- Keep it as a reference page — no architecture discussion, just API surface

Source of truth: `src/Builder.php` + `docs/spec-attributes.md` (Tri-mode Builder section)

### 2. New guide page: `guide/modes.md` — "Processing Modes"

Top-level explanation of the three modes for users choosing between them.

Sections:
- **Overview** — what modes are, when you'd pick each
- **Classic** (default) — annotations + attributes via Generator pipeline. Stable, production.
- **Spec** (beta) — pure PHP 8.1+ attributes, new pipeline (Assembler → Augmenters → Compiler). Cleaner attribute API, typed DTOs, version-aware compilers.
- **Hybrid** (beta) — classic scanning + spec augmenters/compilers. Transition path for existing projects.
- **Switching modes** — CLI (`--mode spec`) and PHP (`$builder->setMode('spec')`)
- **Behavioral differences** — brief table of known differences (link to spec-attributes.md for full details)
- **Migration path** — classic → hybrid (safe, minimal changes) → spec (new attributes)

This is a guide/conceptual page, not reference. Keep it practical — "when should I use this?"

### 3. New guide page: `guide/spec-attributes.md` — "Using Spec Attributes"

The spec-mode equivalent of `guide/using-attributes.md`. Shows how to write code with the new `OpenApi\Spec` namespace.

Sections:
- **Namespace** — `use OpenApi\Spec as OA;`
- **Basic example** — minimal working API (Info + one endpoint)
- **Schemas** — class-level `#[OA\Schema]`, properties inferred from PHP types
- **Operations** — typed subclasses (`OA\Operation\Get`, etc.), path inference
- **PathItem** — controller grouping, prefix composition, shared tags/security
- **Components** — container for non-root DTOs (Parameters, Headers, etc.)
- **Inheritance** — allOf composition via class hierarchy, trait/interface handling
- **Differences from classic** — what's simpler, what changed (link to full table in spec-attributes.md)

### 4. Update `README.md`

Minimal additions — make the new modes discoverable without overshadowing the stable default:

- In Features section: add bullet about spec-attributes pipeline (beta)
- In Usage section: add a brief "Spec Attributes (Beta)" subsection after the existing PHP usage showing the Builder with `setMode('spec')` and a link to the guide
- Keep classic as the primary/default path throughout

### 5. New reference page: `reference/architecture.md` — "Spec Pipeline Architecture"

This is the permanent home for the architectural content currently in the tracking doc (`spec-attributes.md`). Written for a public audience — users who want to understand internals, write custom augmenters, or debug pipeline behavior.

Sections:
- **Pipeline overview** — the 5-stage diagram (Source → Assembler → Specification → Augmenters → Compiler → Output)
- **Assembler** — two-pass nesting (merge/contains slot maps), root attributes, what it doesn't do (no inference)
- **Specification** — flat typed container, bucket list
- **Augmenters** — grouped pipeline (resolve → reduce → augment), how to configure/extend
- **Compilers** — version-aware output generation (3.0, 3.1, 3.2)
- **Reflectors as glue** — why DTOs carry reflectors, how augmenters use them for cross-bucket relationships
- **DTO class tree** — the attribute hierarchy diagram (currently in tracking doc)
- **Classic processor mapping** — table showing which classic processor maps to which augmenter (useful for migration understanding)

This absorbs the "Architecture", "Key design decisions", "Reflectors as relationship glue", "Classic processor mapping", and "DTO tree" sections from the tracking doc.

### 6. Update `guide/generating-openapi-documents.md`

Currently only shows classic usage. Add:
- A section after "Using the Builder" showing spec mode usage
- Brief mention that `--mode spec` is available on CLI

### 7. Add spec tab to `<codeblock>` component + snippets

The existing `Codeblock.vue` component renders tabbed code examples (Attributes / Annotations). Extend it with a third "Spec" tab:

- Update `docs/.vitepress/theme/components/Codeblock.vue` — add a `spec` slot/tab (conditionally rendered, so existing two-tab codeblocks aren't affected)
- Add `_spec.php` snippet variants alongside existing `_at.php` / `_an.php` files in `docs/snippets/guide/`
- Extend the examples pages where spec examples already exist (`docs/examples/`) — these already have `spec/` subdirectories

The spec tab should only appear when the slot has content (backwards-compatible with existing pages that only have `at` + `an`).

Convention: snippet files use `_spec.php` suffix (matching existing `_at.php` / `_an.php` pattern).

### 8. Visual indicator for spec-related nav items

Add a visual cue to sidebar items in the spec section so users can immediately tell what's beta/new:

Options (pick one during implementation):
- **Badge** — VitePress sidebar supports `{ text: 'Modes', link: '...', badge: 'Beta' }` (built-in since VitePress 1.x) — renders a small colored badge next to the item
- **Emoji prefix** — e.g. `⚡ Using Spec Attributes` — simple, no custom CSS, works everywhere
- **Custom CSS class** — add `.spec-beta` class to sidebar items, style with a subtle colored dot or tag

Recommendation: Use VitePress's built-in badge feature if available in the project's VitePress version, otherwise emoji prefix as fallback. The badge/indicator should say "Beta" to clearly communicate maturity.

### 9. Sidebar updates (`docs/.vitepress/config.js`)

Guide sidebar — new "Spec Attributes (Beta)" section:
```js
{
  text: 'Spec Attributes',
  badge: 'Beta',  // or equivalent visual cue
  items: [
    { text: 'Processing Modes', link: '/guide/modes' },
    { text: 'Using Spec Attributes', link: '/guide/spec-attributes' },
  ]
}
```

Reference sidebar additions:
- Add "Architecture" under "Api" group (with beta badge)

## Pages NOT needed separately

- **PathItem details** — covered as a section within `guide/spec-attributes.md` (PathItem pattern, prefix composition, shared metadata). The augmenter reference already documents PathItems augmenter config. The tracking doc's "PathItem design" section content lands here.
- **Components details** — same: a section in `guide/spec-attributes.md` covers the container pattern with examples. The tracking doc's "Components container" section content lands here.
- **Inheritance details** — covered via `guide/spec-attributes.md` (Inheritance section) + augmenter reference. The tracking doc's "Inheritance expansion" rules content lands here.

These are important topics but don't warrant standalone pages — they'd be too thin. If they grow, split later.

## Content from tracking doc → final destination

| Tracking doc section | Lands in |
|---|---|
| Architecture (5-stage pipeline) | `reference/architecture.md` |
| Key design decisions (slot maps, two-pass, roots, immutable) | `reference/architecture.md` |
| Reflectors as relationship glue | `reference/architecture.md` |
| DTO class tree | `reference/architecture.md` |
| Classic processor mapping table | `reference/architecture.md` |
| Tri-mode Builder | `reference/builder.md` + `guide/modes.md` |
| Augmenter status table | Already in `reference/augmenters.md` (auto-generated) |
| Example coverage table | Drop (internal tracking only) |
| Behavioral differences table | `guide/modes.md` (brief) |
| PathItem design | `guide/spec-attributes.md` § PathItem |
| Components container | `guide/spec-attributes.md` § Components |
| Inheritance expansion rules | `guide/spec-attributes.md` § Inheritance |
| What's next / Shipping / Version timeline | Drop (internal roadmap, not user docs) |
| Extension systems | Drop for now (not implemented yet) |
| Consequences | Drop (ADR-style, not user-facing) |

## Order of work

1. `.vitepress/config.js` + `Codeblock.vue` — sidebar structure + spec tab support (scaffolding first)
2. `reference/builder.md` — quick update, unblocks everything else
3. `guide/modes.md` — foundational, referenced by other pages
4. `guide/spec-attributes.md` — the main user-facing guide
5. Spec snippets — `_spec.php` variants for existing guide codeblocks
6. `guide/generating-openapi-documents.md` — quick update
7. `reference/architecture.md` — depth page (absorbs tracking doc content)
8. `README.md` — final, once we're confident in the messaging

## Constraints

**Future-proof for v6/v7 docs split.** The structure must allow a clean split into "v6 (classic-default)" and "v7+ (spec-default)" doc sets without restructuring:
- Spec content lives in its own sidebar section, not interleaved with classic pages
- Classic pages (`guide/using-attributes.md`, `reference/attributes.md`, `reference/processors.md`) stay untouched — easy to freeze as "v6 docs"
- `reference/builder.md` and `guide/modes.md` describe all modes neutrally — in v7, flip one sentence ("spec is now the default") rather than rewriting
- No spec examples woven into classic guides

## Scope

This is for the current state (beta). We deliberately do NOT:
- Write migration guides yet (those come when spec mode is promoted to default in v7)
- Document extension systems (AttributeEnricher, CompilerExtension) — not implemented yet
- Remove/deprecate classic docs — classic remains default and fully supported

### Future plans (out of scope now)

- **v7 docs split** — when spec becomes default, freeze current classic docs as "v6" archive and promote the spec section to primary. The sidebar section structure makes this a config change, not a rewrite.
- **Migration guide** — `guide/migrating-to-v7.md` covering classic → spec migration. Depends on the pipeline being stable/non-beta.
- **Extension docs** — AttributeEnricher, CompilerExtension, custom Attachables. Blocked on implementation.
