# SEO Open Graph images (`og:image`)

Goal: every page exposes an `og:image` so links shared on social platforms,
chat apps and search previews carry the lychen branding. The target visual is
**the page hero photo with the lychen logo centered on top**, in the standard
1200×630 Open Graph ratio.

## How it is wired today

`og:image` (and the Twitter fallback) is emitted from two layers:

- **Site-wide fallback** — `src/App.vue` sets a default `og:image`
  (`src/assets/og-default.webp`) via `useSeoMeta`. Any page that does not declare
  its own image — and any page added in the future — still embeds a branded
  thumbnail.
- **Per-page override** — a view passes its own image to `useExtendedHead`:

  ```ts
  import ogImageUrl from './assets/MyPageOgImage.webp';

  useExtendedHead(t, { ogImage: ogImageUrl });
  ```

  `useExtendedHead` (`@lychen/vue-unhead-composables`) resolves the import to an
  absolute URL (crawlers require absolute URLs) and also wires `og:url`,
  `twitter:card=summary_large_image`, title and description.

Pages with a dedicated image today: `home`, `label`, `mission`. Pages currently
relying on the fallback: `team`, `charter`, `partnerships`, `applications`.

## Placeholders

`src/assets/og-default.webp` is a **placeholder** (a copy of the home OG image)
so embeds are branded immediately. Replace it, and add per-page images, with the
real "hero + centered logo" renders. Keep them `1200×630` `.webp` to match the
existing assets.

## Suggested automation (generate the images via Claude + a connector)

Two complementary options, from lightest to most automated:

1. **Build-time generation (no external image model).** Add a Vite/SSG step that
   renders each OG card from HTML/SVG using [`satori`](https://github.com/vercel/satori)
   + [`@resvg/resvg-js`](https://github.com/yisibl/resvg-js): compose
   `hero photo → dark overlay → centered logo-lychen.svg → page title`, export
   `1200×630` `.webp`. Fully deterministic, runs in CI, no API cost. This is the
   recommended default because the desired visual (hero + centered logo) is a
   simple, templatable composition.

2. **Claude-driven generation via an MCP connector.** When a page needs net-new
   artwork (no suitable hero photo), drive an image-generation connector
   (e.g. a Gemini/OpenAI image MCP server, or a Figma/Cloudinary connector
   exposed over MCP) from a small script: Claude reads the page title/description
   + brand kit (`libs/assets/lychen/logos/logo-lychen.svg`, color tokens in
   `libs/css/core`), prompts the connector for a 1200×630 background on-brand,
   then composites the centered logo with the option-1 pipeline for pixel-exact
   placement. The connector handles pixels; Claude handles per-page intent and
   keeps the logo overlay consistent.

A practical path: ship option 1 as a `moon website:generate-og-images` task that
templates every route, and reach for option 2 only for pages that lack a hero
photo.
