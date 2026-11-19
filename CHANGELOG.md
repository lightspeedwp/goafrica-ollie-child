# Changelog

## Unreleased

- Port slider, carousel, gallery and mega menu styling that only existed on `main` and never reached the live site:
  - Style the Carousel block (Swiper) arrows and dots to match the Tour Operator sliders, and hide its arrows on mobile.
  - Wrap slider dots that overflow, and clip sliders so the single mobile slide shows full width.
  - Lay out Tour Operator galleries as a 3, 2 and 1 column grid of 4:3 images.
  - Add hover and focus states to the travel information cards.
  - Add the Mega Menu Nav block style used by the destination and travel style dropdown menus.
- Show FAQ answers and travel style card excerpts in the editor.
- Fix the footer navigation font weight (`regular` is not a valid CSS value).

## 1.1.0 - 2026-11-09

- Sync the live Site Editor templates, template parts and patterns into the theme files (PR #2).
- Add a main landmark to the single, accommodation and taxonomy archive templates (PR #2).
- Show "Op aanvraag" for accommodation with no price, and fix the empty tour card value (PR #1).
- Set a real theme version so enqueued assets are cache-busted by release.

## 1.0.0

- Initial release.
