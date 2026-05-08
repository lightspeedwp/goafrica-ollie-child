# GoAfrica.ml Block Theme Agent Guide

## Purpose

This file guides coding agents working on the `GoAfrica.ml` block theme for `goafrica.nl`.
It is written for a WordPress block-theme workflow and is grounded primarily in the attached implementation evidence from `agent_files/theme.json`.

## Current Evidence Status

- Strongest verified implementation source: `agent_files/theme.json`
- Verified project type: WordPress block theme with tokenized color and semantic custom settings
- Verified design-system areas: palette, semantic color aliases, breakpoints, motion tokens, elevation tokens, and button padding tokens
- Missing primary sources: live repository tree, Figma design file, Google Drive brief or governance notes
- Required caution: treat undocumented component structure, typography family choices, and repo layout conventions as provisional until repo evidence is supplied

## Source Authority

Use sources in this order when they exist:

1. Active project `theme.json` and style variation files
2. Block-specific files such as `block.json`, patterns, template parts, and CSS
3. Approved `DESIGN.md` package files for normalized design intent
4. Figma variables and components for design intent not yet implemented
5. Supporting briefs or governance docs

If two sources disagree, implementation files are authoritative for current shipped behavior and Figma is authoritative for intended design direction.

## Theme Token Rules

### Palette

The verified preset palette includes:

- neutrals from `neutral-100` through `neutral-900`
- dark surfaces from `surface-100` through `surface-900`
- brand blues from `brand-100` through `brand-900`
- CTA cyans from `cta-100` through `cta-900`
- accent green, magenta, lime, and yellow families
- state colors: `error-foreground`, `warning-foreground`, `information-foreground`, `success-foreground`

### Semantic custom color aliases

Prefer semantic custom tokens before hardcoding preset slugs in template or block code. Verified aliases include:

- `--wp--custom--color--text--default`
- `--wp--custom--color--text--inverse`
- `--wp--custom--color--text--muted`
- `--wp--custom--color--surface--canvas`
- `--wp--custom--color--surface--card`
- `--wp--custom--color--button--fill--background`
- `--wp--custom--color--button--outline--background`
- `--wp--custom--color--focus--ring`
- `--wp--custom--color--effect--hero--brand`

### Implementation expectations

- Prefer `var:preset|color|...` and `var(--wp--custom--...)` tokens over raw hex values.
- Preserve both primitive preset slugs and semantic custom aliases when they serve different jobs.
- Treat missing semantic aliases as token drift and document them before adding new raw colors.
- Reuse the existing semantic naming shape for any new token branches.

## Layout and Responsive Rules

Verified breakpoint tokens:

- `full: 1440px`
- `desktop: 1280px`
- `tablet-landscape: 1024px`
- `tablet-portrait: 768px`
- `mobile-landscape: 640px`
- `mobile-portrait: 390px`
- `mobile-compact: 320px`

When implementing responsive behavior:

- align new layout decisions to the existing breakpoint names
- avoid introducing one-off viewport constants
- prefer reusable pattern or block spacing that can be traced back to theme tokens

## Typography Rules

Verified custom typography tokens currently cover:

- letter-spacing scale from `tighter` to `widest`
- font-weight scale from `thin` through `black`

Not yet verified from attached implementation evidence:

- font families
- font-size scale
- line-height scale

Until stronger evidence is supplied:

- preserve any existing theme typography settings if found in the repo
- do not rename typography tokens casually
- document inferred typography additions in `DESIGN.md` and validation notes

## Motion and Elevation Rules

Verified custom motion tokens include duration, delay, easing, runtime easing, and active-scale values.
Verified shadow tokens include elevation tiers, heading shadows, interactive accent shadow, and card hover shadow.

Implementation guidance:

- prefer these motion tokens instead of ad hoc transition timings
- keep motion subtle and content-supporting
- preserve the current dark-shadow styling language when creating new raised surfaces

## Block Theme Authoring Rules

- Prefer WordPress-native configuration in `theme.json`, style variations, block styles, patterns, and template parts.
- Keep reusable presentation values inside tokens when a value is meant to repeat.
- Use semantic names for reusable surfaces, text, borders, focus, and button states.
- Map component-level styling to block patterns or block styles before adding custom wrappers.
- Keep button styling aligned with the verified fill and outline token groups.

## Accessibility Guardrails

- Maintain strong contrast between `text.default` and its surface tokens.
- Preserve a visible focus treatment using the existing focus ring token.
- Do not rely on accent color alone to communicate meaning.
- When introducing disabled, error, success, or warning states, tie them back to documented semantic tokens.
- Flag any accessibility claim as unverified if it cannot be checked against the actual rendered theme.

## Required Workflow

1. Inspect `theme.json` and any touched block or template files before changing token names.
2. Reuse existing preset and custom token families when possible.
3. Record any new semantic token or alias in `DESIGN.md` and `design-md-source-map.md`.
4. Update `design-md-validation-report.md` when a change introduces drift, risk, or new inference.
5. Prefer small, traceable changes over broad restyling when source evidence is partial.

## Known Gaps

- No public repository context was available during drafting.
- No verified Figma file was available during drafting.
- No verified component inventory was available beyond the theme token structure.

Agents should therefore treat repo structure, naming beyond the attached theme tokens, and exact component compositions as provisional until the GoAfrica theme repository or ZIP archive is supplied.
