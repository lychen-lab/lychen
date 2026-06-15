# Design system audit — current state & recommendations

> Scope: how CSS variables and the design-token layer are structured across the
> monorepo, how dark/light theming works, and how the system can evolve to serve
> several apps that share one design language but differ by brand (logo, accent
> colour, …). Includes a survey of common industry practice and a phased
> proposal.
>
> Status: **report / proposal** — no runtime behaviour is changed by this
> document.

## 1. Executive summary

Lychen already has a **solid, modern, well-centralised foundation**: a single
CSS-core library (`@lychen/css-core`) exposes the whole token set in the OKLCH
colour space, dark/light is fully token-driven through a `data-theme` attribute,
and Tailwind v4's CSS-first `@theme` makes every token available as a utility
class. There is essentially **no token duplication** across the six apps — they
all import the exact same stylesheet.

The main gap is **multi-brand differentiation**. Ten per-app accent colours are
defined, but they are consumed in exactly one place (the marketing ecosystem
diagram). Every app renders with an identical `primary`/`surface` palette, and
there is no mechanism to give Tera, Espace, Myko… their own primary colour, logo
or accent without editing shared CSS. The token layer is also only **two tiers**
(theme-mode → role), which makes a third "brand" dimension awkward to add.

The recommendations below keep everything that works and add a **brand layer**
on top of the existing semantic tokens, following the now-standard three-tier
token model (primitive → semantic → brand/mode override).

## 2. Current state

### 2.1 Architecture at a glance

```
libs/css/core/                     → published as @lychen/css-core
  all.css            ← entry point: imports Tailwind + every token file
  colors.css         ← all colour tokens (light + dark) + @theme mapping
  gradients.css      ← gradient tokens
  keyframes.css      ← animation tokens
  components/
    fonts.css        ← @font-face + --font-* theme tokens
    grid.css         ← fluid grid tokens
    container.css    ← container utility
    scrollbar.css    ← scrollbar styling
    icon.css         ← Lucide icon sizing (18×18)

every app  →  src/stylesheet/main.css  →  @import '@lychen/css-core/all';
```

Tailwind v4 is wired purely through the `@tailwindcss/vite` plugin; there is **no
`tailwind.config.js`** anywhere. All configuration lives in CSS (`all.css`),
which is the v4-native approach.

### 2.2 Colour tokens (`libs/css/core/colors.css`)

Tokens are authored in **OKLCH**, which gives perceptually even lightness/chroma
steps and makes the light↔dark transforms predictable. Two naming layers exist:

- **Raw vars** `--lychen-color-*` — the source of truth, redefined per theme.
- **Tailwind tokens** `--color-*` — mapped 1:1 from the raw vars inside an
  `@theme inline { … }` block, which is what generates the `bg-primary`,
  `text-on-surface`, `bg-app-tera`, … utilities.

Semantic roles follow a Material-style `role` / `on-role` / `role-container` /
`on-role-container` quartet:

| Role group | Tokens |
|---|---|
| Brand | `primary`, `secondary`, `tertiary` (+ `on-*`, `*-container`, `on-*-container`) |
| Status | `negative`, `positive`, `warning` (+ same variants) |
| Surfaces | `surface`, `on-surface`, `surface-container{,-low,-lowest,-high,-highest}` |
| Per-app accents | `app-espace`, `app-tera`, `app-myko`, `app-meli`, `app-kiro`, `app-humu`, `app-novi`, `app-vara`, `app-kolo`, `app-robust` |

The **surface ramp is computed**, not hand-picked: a single
`--lychen-color-surface-lightness` (0.98 light / 0.15 dark) with constant
`--lychen-color-surface-chroma` (0.014) and `--lychen-color-surface-hue` (93.57)
drives every surface step via `oklch(...)`. This is an elegant, low-maintenance
pattern.

### 2.3 Dark / light mode

Attribute-based, with a system-preference fallback:

- `libs/css/core/all.css` declares the variant:
  `@custom-variant dark (&:where([data-theme='dark'], [data-theme='dark'] *));`
- `colors.css` ships two value sets: `:root, [data-theme='light']` and
  `[data-theme='dark']`.
- `libs/vue/color-scheme/composables/usePreferredColorScheme.ts` uses
  `@vueuse/core` `useDark({ selector: 'html', attribute: 'data-theme', valueDark:
  'dark', valueLight: 'light' })`, seeds from `prefers-color-scheme`, and
  persists to `localStorage['vueuse-color-scheme']`.
- `ToggleColorScheme.vue` is the UI control.

This is robust: SSG/SSR-friendly, no FOUC-prone JS-only theming, and entirely
token-backed (components never branch on the theme themselves).

### 2.4 Multi-brand today

This is the thin spot. The ten `--lychen-color-app-*` accents are the *only*
per-app concept, and they are referenced in a single component —
`projects/website/src/views/home/PageHomeUseCases.vue` — to colour the nodes of
the ecosystem diagram:

```ts
const APP_NODE_COLOR: Record<string, string> = {
  espace: 'var(--color-app-espace)',
  tera: 'var(--color-app-tera)',
  // …
};
```

Beyond that diagram, **every app is visually identical**: same `primary`, same
surfaces, same single Lychen logo (`libs/assets/lychen/logos/`, no per-app
variants). There is no `[data-brand]` / per-app override layer, so giving an app
its own primary colour would today mean reassigning shared `--lychen-color-*`
vars (global) or forking CSS.

### 2.5 How components consume tokens

Components are headless (**Reka UI**) and styled with Tailwind utilities that map
to the tokens. Variant logic lives in typed constant objects (a CVA-style
pattern) plus shared "presets":

```ts
// libs/vue/components-core/button/index.ts (variants)
'bg-on-surface text-surface hover:bg-on-surface/90'   // example variant
'h-10 px-4 py-2 rounded-2xl text-md'                  // example size

// libs/vue/components-core/utils/Preset.ts
PRESETS.FocusOutline // 'focus:outline-1 focus:outline-offset-2 focus:outline-on-surface/70'
PRESETS.InputField   // 'rounded-2xl bg-surface-container-highest text-on-surface-container-highest'
```

Because variants reference **semantic** tokens (`on-surface`, `surface`,
`app-*`) rather than raw colours, a brand/mode remap at the token layer
propagates to every component automatically — which is exactly the property a
brand layer needs.

### 2.6 Other tokens

- **Typography:** three variable fonts (OpenSans, Inter, Lexend) registered in
  `components/fonts.css` and exposed as `--font-*`. There is **no explicit
  type scale** (sizes/line-heights) — components reach for Tailwind defaults
  (`text-sm`, `text-md`, …).
- **Spacing:** components use `--spacing` (e.g. `calc(var(--spacing) * 2)`), but
  that variable is **not defined in css-core** — it comes from Tailwind/Reka
  defaults. The spacing scale is therefore implicit and undocumented.
- **Grid / container / scrollbar / icon:** small, focused token files; icon size
  is hardcoded to 18px with no token.

### 2.7 Documentation surfaces

- `projects/robust/website` is the "design system" project, but in practice it is
  a **marketing/SSG site** (home + ecosystem pages, `CardActor.vue`) — not an
  interactive component/token gallery.
- `projects/storybook` (Storybook 10) exists but documents **only ~2 stories**
  and its `src/style.css` is the **default Vite scaffold stylesheet** with
  hardcoded colours (`#242424`, `#646cff`, `#1a1a1a`, …) that ignore the design
  tokens. Token/theme/brand coverage is effectively absent.

## 3. Strengths (keep these)

1. **Single source of truth** — one css-core lib, imported verbatim by all apps;
   near-zero duplication.
2. **OKLCH everywhere** — perceptually uniform, enables the computed surface ramp
   and predictable dark-mode derivation.
3. **CSS-first Tailwind v4** — no JS config drift; tokens *are* the utility API.
4. **Clean dark-mode model** — attribute-driven, token-backed, system-aware,
   SSR-safe.
5. **Semantic component styling** — variants reference roles, not raw hex/oklch,
   so a token remap cascades for free.

## 4. Gaps & inconsistencies

| # | Finding | Location | Impact |
|---|---|---|---|
| G1 | No per-app brand mechanism; apps are visually identical apart from the diagram | `colors.css`, all apps | Can't brand Tera/Espace/… without editing shared CSS |
| G2 | Token model is only 2-tier (mode → role); no primitive palette and no brand axis | `colors.css` | Hard to add a brand dimension cleanly; magic oklch literals repeated |
| G3 | `--spacing` consumed but never defined in css-core | components, `grid.css` | Implicit scale; opaque dependency on Tailwind/Reka defaults |
| G4 | No documented type scale | `components/fonts.css` | Sizing decided ad hoc per component |
| G5 | Storybook `src/style.css` is leftover Vite scaffold with hardcoded colours | `projects/storybook/src/style.css` | Contradicts the token system; should be removed/replaced |
| G6 | Design-system documentation is thin (no token/brand gallery) | `robust/website`, `storybook` | Hard for contributors to discover tokens & brand rules |
| G7 | Dual naming (`--lychen-color-*` → `--color-*`) is undocumented | `colors.css` | New contributors unsure which layer to edit |
| G8 | Per-app accents have no light/dark contrast contract documented | `colors.css` | Accessibility of `app-*` on surfaces unverified |

## 5. How other teams handle this (industry practice)

The consensus pattern for a shared-design / multi-brand system is a **three-tier
token architecture**, with theme *and* brand expressed as overrides of the
middle tier:

1. **Primitive / global tokens** — raw values (`--blue-500: oklch(…)`), no
   meaning. One palette, brand-agnostic.
2. **Semantic / alias tokens** — intent, not appearance
   (`--color-action-primary`, `--color-bg-surface`). Components only ever use
   these.
3. **Component tokens** (optional) — component-scoped (`--button-bg`) pointing at
   semantic tokens.

Dark mode and multi-brand are then *the same move*: keep the semantic names
fixed and **remap what they point to**. Dark mode remaps via
`[data-theme='dark']`; a brand remaps via `[data-brand='tera']`. Components and
the semantic layer stay byte-for-byte identical; you "ship one codebase with
multiple token sets". This is precisely how white-label / multi-tenant systems
are built on Tailwind v4 — define base `@theme` variables, then override them
under `[data-theme]` / `[data-brand]` selectors (no per-build forks needed).

Tooling/standards worth noting:
- The **Design Tokens Community Group** published the first **stable** Design
  Tokens spec (2025.10) — a vendor-neutral JSON format for sharing tokens with
  Figma/Penpot/Style Dictionary. Exporting tokens in this format lets design and
  code share one source.
- Tailwind v4's `@theme` + OKLCH is explicitly designed for this CSS-variable
  remap style of theming, which Lychen already uses for dark mode.

Lychen is ~80% of the way there: it has tiers 2 (semantic) and the mode-override
mechanism; what's missing is an explicit tier 1 (primitives) and the **brand
override axis** on tier 2.

Sources:
- [Tailwind CSS v4: Multi-Theme Strategy — simonswiss](https://simonswiss.com/posts/tailwind-v4-multi-theme/)
- [How to create multi theme using Tailwind v4 — tailwindcss discussion #15222](https://github.com/tailwindlabs/tailwindcss/discussions/15222)
- [Design Tokens & Theming: Scalable UI Systems in 2025 — materialui.co](https://materialui.co/blog/design-tokens-and-theming-scalable-ui-2025)
- [Design Token Naming Best Practices — Netguru](https://www.netguru.com/blog/design-token-naming-best-practices)
- [Design Tokens Specification reaches first stable version (2025.10)](https://designzig.com/design-tokens-specification-reaches-first-stable-version-with-w3c-community-group/)
- [Design Tokens That Scale in 2026 (Tailwind v4 + CSS Variables) — Mavik Labs](https://www.maviklabs.com/blog/design-tokens-tailwind-v4-2026/)

## 6. Recommendations

Ordered by value/effort. None require breaking the current utility API.

### R1 — Add a brand override layer (highest value) ⭐

Introduce a `data-brand` axis that remaps the **semantic** brand tokens per app,
mirroring the existing `data-theme` mechanism. Each app already has an accent
(`--lychen-color-app-<name>`); promote it to that app's `primary` (and derive the
`on-`/`-container` companions in OKLCH from the same hue):

```css
/* libs/css/core/colors.css — sketch */
[data-brand='tera'] {
  --lychen-color-primary: var(--lychen-color-app-tera);
  --lychen-color-on-primary: oklch(0.98 0.03 145);
  --lychen-color-primary-container: oklch(0.85 0.10 145);
  --lychen-color-on-primary-container: oklch(0.15 0.16 145);
}
[data-brand='espace'] { /* …app-espace hue 175… */ }
```

Set `data-brand` on `<html>` once per app (a tiny composable parallel to
`usePreferredColorScheme`, or a static attribute in each app's `index.html`).
Because `data-theme` and `data-brand` are independent attributes, brand × mode
combine without extra work. Components need **no changes** — they already use
`bg-primary` etc.

### R2 — Make the token model explicitly three-tier

Extract the repeated raw OKLCH literals into a **primitives** file
(`components/palette.css`: `--lychen-palette-green-500`, …), and have the
semantic `:root` / `[data-theme]` / `[data-brand]` blocks reference *those*
instead of inline `oklch(...)`. This removes magic numbers, documents the palette
once, and makes brand/mode overrides trivial. Document the
`--lychen-color-*` (authoring) → `--color-*` (Tailwind) relationship in a short
header comment (addresses G2, G7).

### R3 — Define spacing & type scales in css-core

Add `components/spacing.css` and a type scale (sizes + line-heights as
`--text-*`/`--leading-*` `@theme` tokens) so sizing is explicit and shared rather
than relying on Tailwind defaults and an undefined `--spacing` (G3, G4).

### R4 — Make Storybook the token/brand gallery

Delete/replace `projects/storybook/src/style.css` (the Vite scaffold) so only
`@lychen/css-core/all` styles Storybook, then add stories that render the colour
roles, surface ramp, typography, and a **brand switcher** (`data-brand`) and
**theme switcher** (`data-theme`) toolbar. This turns Storybook into the living
documentation that's currently missing (G5, G6).

### R5 — Per-app brand assets

Establish a convention for per-app logos/assets (e.g.
`libs/assets/<app>/logos/`) and a `BrandLogo` component that resolves the current
brand, so logo/imagery differ per app alongside colour (G1).

### R6 — Export tokens in the W3C Design Tokens format (optional)

If design uses Figma/Penpot, generate a DTCG-format `tokens.json` from css-core
(or author it there and build CSS via Style Dictionary) so design and code share
one source of truth.

### Suggested phasing

- **Phase 1 (foundation):** R2 (primitives) → R1 (brand layer) for one pilot app
  (e.g. Tera). Validate brand × dark/light contrast.
- **Phase 2 (consistency):** R3 scales, roll R1 out to all apps, R5 assets.
- **Phase 3 (documentation/tooling):** R4 Storybook gallery, R6 token export.

## 7. Appendix — key files

| Concern | Path |
|---|---|
| Token entry point | `libs/css/core/all.css` |
| Colour tokens (light/dark) + `@theme` | `libs/css/core/colors.css` |
| Gradients / keyframes | `libs/css/core/gradients.css`, `keyframes.css` |
| Fonts / grid / container / scrollbar / icon | `libs/css/core/components/*.css` |
| Dark/light composable + toggle | `libs/vue/color-scheme/composables/usePreferredColorScheme.ts`, `components/ToggleColorScheme.vue` |
| Example token usage in components | `libs/vue/components-core/button/index.ts`, `utils/Preset.ts` |
| Per-app accent usage | `projects/website/src/views/home/PageHomeUseCases.vue` |
| Design-system site | `projects/robust/website/` |
| Storybook (scaffold CSS to replace) | `projects/storybook/src/style.css`, `.storybook/storybook.css` |
