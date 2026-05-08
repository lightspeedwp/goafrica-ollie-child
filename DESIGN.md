---
version: alpha
name: GoAfrica.ml Block Theme
description: Provisional design contract for the goafrica.nl WordPress block theme, grounded in attached theme token evidence and LightSpeed documentation standards.
colors:
  primary: "#1E6AFF"
  secondary: "#00FCFC"
  tertiary: "#22C78A"
  base: "#FAFAFA"
  contrast: "#080808"
  surface-strong: "#12121A"
  neutral-border: "#D8D8D8"
  accent-two: "#FC00FC"
  error: "#EF4444"
  warning: "#F59E0B"
  information: "#3B82F6"
  success: "#10B981"
typography:
  body-md:
    fontFamily: System UI
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.5
  label-md:
    fontFamily: System UI
    fontSize: 14px
    fontWeight: 500
    letterSpacing: 0.025em
rounded:
  sm: 4px
  pill: 999px
spacing:
  button-block: 1rem
  button-inline-lg: 4rem
  button-inline-sm: 1.5rem
components:
  button-fill:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.base}"
    typography: "{typography.label-md}"
    rounded: "{rounded.pill}"
    padding: "{spacing.button-block}"
  button-outline:
    backgroundColor: "{colors.surface-strong}"
    textColor: "{colors.primary}"
    typography: "{typography.label-md}"
    rounded: "{rounded.pill}"
    padding: "{spacing.button-block}"
---

# GoAfrica.ml Block Theme Design Contract

## Overview

### Metadata

- Project: `GoAfrica.ml` block theme for `goafrica.nl`
- Evidence confidence: Medium
- Overall evidence status: Mixed
- Known conflicts: None inside the attached implementation source; broader repo and Figma authority are missing
- Missing critical evidence: public repo or repo ZIP, Figma variables and components, approved typography source, component inventory

### Source Summary

- Strongest verified source: attached [`theme.json`](/workspace/agent_files/theme.json)
- Supporting standards sources: LightSpeed specification, WordPress mapping guidance, accessibility checkpoints
- Missing primary design sources: Figma and Google Drive design references

### Design System Foundations

- Design intent: A bright, travel-oriented WordPress theme that pairs a mostly light reading surface with saturated brand, CTA, and accent color families.
- Brand personality: Energetic, globally oriented, editorial, and conversion-aware.
- Evidence status: Mixed
- Source references:
  - Verified implementation evidence from `agent_files/theme.json`
  - Inferred product framing from the project name and destination website

## Colors

- **Primary**
  - Token value: `brand-500` / `#1E6AFF`
  - Evidence label: Verified
  - Source reference: `settings.color.palette`
- **Secondary**
  - Token value: `cta-500` / `#00FCFC`
  - Evidence label: Verified
  - Source reference: `settings.color.palette`
- **Tertiary**
  - Token value: `accent-500` / `#22C78A`
  - Evidence label: Verified
  - Source reference: `settings.color.palette`
- **Neutral**
  - Token value: `base` / `#FAFAFA` with `contrast` / `#080808`
  - Evidence label: Verified
  - Source reference: `settings.color.palette`

Additional verified system behavior:

- Text roles are mapped through semantic aliases such as `text.default`, `text.inverse`, `text.muted`, `text.brand`, and `text.brand-strong`.
- Surface roles are mapped through semantic aliases such as `surface.canvas`, `surface.card`, and `surface.card-raised`.
- State colors exist for error, warning, information, and success foreground usage.
- Focus styling has a dedicated `focus.ring` token.

Token-drift note:

- No token drift was provable inside the attached file alone.
- Drift cannot yet be assessed against live template, CSS, or block files because the repo source was not available.

## Typography

- **Headline**
  - Token reference: weight scale through `settings.custom.typography.font-weight`; actual family and size not verified
  - Evidence label: Mixed
  - Source reference: `settings.custom.typography.font-weight`
- **Body**
  - Token reference: `{typography.body-md}`
  - Evidence label: Inferred
  - Source reference: no font family or size scale present in attached implementation evidence
- **Label / Caption**
  - Token reference: `{typography.label-md}`
  - Evidence label: Mixed
  - Source reference: letter-spacing is verified from `settings.custom.typography.letter-spacing`; family and size are inferred

Typography guidance:

- Verified typography evidence currently supports weight and letter-spacing scales, not full type ramp definitions.
- The YAML typography tokens above are provisional placeholders to satisfy package structure and should be replaced once the repo or Figma confirms font family, size, and line-height choices.

## Layout & Spacing

- Grid model: breakpoint-led responsive layout with named viewport tiers from `mobile-compact` through `full`
- Spacing model: verified button padding tokens of `1rem` top and bottom, `4rem` right, and `1.5rem` left, with broader spacing scale still missing
- Evidence label: Mixed
- Source reference:
  - `settings.custom.layout.break-points`
  - `settings.custom.spacing.button`

Responsive breakpoint set:

- `full: 1440px`
- `desktop: 1280px`
- `tablet-landscape: 1024px`
- `tablet-portrait: 768px`
- `mobile-landscape: 640px`
- `mobile-portrait: 390px`
- `mobile-compact: 320px`

## Elevation & Depth

- Approach: subtle shadow-led depth rather than thick borders alone
- Evidence label: Verified
- Source reference: `settings.custom.shadow`

Verified depth tokens include:

- elevation tiers `100` through `600`
- heading text-shadow variants for contrast and base surfaces
- interactive accent glow shadow
- elevated card hover shadow

## Shapes

- Shape language: rounded interactive controls are likely intended, but exact radius scale is not verified from the attached implementation source
- Radius usage: YAML `rounded` values are provisional placeholders until a repo or design source confirms them
- Evidence label: Inferred
- Source reference: no radius tokens present in attached implementation evidence

## Components

### Component: Button Fill

- Purpose: primary call-to-action treatment
- Variants / states: default and hover semantic token pairs are verified
- Key token references:
  - `button.fill.background`
  - `button.fill.text`
  - `button.fill.border`
  - `button.fill.background-hover`
  - `button.fill.text-hover`
- Evidence label: Verified
- Source references: `settings.custom.color.button.fill`
- Implementation mapping: WordPress button styles should map to theme tokens instead of raw colors

### Component: Button Outline

- Purpose: secondary call-to-action treatment on dark or high-contrast surfaces
- Variants / states: default and hover semantic token pairs are verified
- Key token references:
  - `button.outline.background`
  - `button.outline.text`
  - `button.outline.border`
  - `button.outline.background-hover`
  - `button.outline.text-hover`
- Evidence label: Verified
- Source references: `settings.custom.color.button.outline`
- Implementation mapping: WordPress outline button styles should preserve semantic background and border references

### Accessibility Considerations

- Focus-ring color is verified, but focus thickness and offset are not.
- Status colors are present, but their rendered contrast cannot be validated without the actual theme output.
- Typography accessibility remains provisional because the actual font stack and size scale are not yet verified.

### WordPress Mapping

- Preset palette tokens belong in `settings.color.palette`.
- Semantic aliases belong under `settings.custom.color.*`.
- Responsive thresholds belong under `settings.custom.layout.break-points`.
- Motion and shadow tokens belong under `settings.custom.animation.*` and `settings.custom.shadow.*`.
- Button behavior should map to block styles or pattern-level button compositions before introducing custom wrappers.

## Do's and Don'ts

### Do

- Do use semantic custom tokens for text, surface, focus, and button roles.
- Do preserve the existing named breakpoint system across responsive work.
- Do keep button implementations aligned with verified fill and outline token groups.
- Do label any new typography or radius choices as inferred until stronger evidence exists.

### Don't

- Do not hardcode raw colors where preset or semantic theme tokens already exist.
- Do not invent a full component library from this file alone.
- Do not treat the provisional typography and radius placeholders in the YAML frontmatter as final design truth.
- Do not claim accessibility compliance beyond what the rendered theme can support.

### Open Questions and Uncertainties

- Which font families and type sizes are approved for the live theme?
- Are there style variation files or custom block styles that extend the token system?
- Which WordPress core blocks or custom blocks make up the main GoAfrica marketing and editorial surfaces?

### Required Follow-up Actions

- Verify typography against the active theme repository or Figma source.
- Replace provisional radius and typography YAML values with evidence-backed values.
- Confirm component inventory from the actual block theme implementation.
