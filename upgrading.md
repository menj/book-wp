# Upgrading Book WP

## From 2.0.x to 2.1.0

No action is required. On the first dashboard visit by an administrator, 2.1.0 copies your 2.0.x Customizer settings (and any remaining 1.x `book_wp_*` options) into a single `bookwp_options` array. The 1.x value `maven` is remapped to `maven-pro` during this step.

**Where settings live now**

| Surface | Location | Purpose |
|---|---|---|
| Theme Options page | Appearance → Theme Options | Primary, tabbed settings page |
| Customizer | Appearance → Customize → Book WP | Live preview of the same settings |

Both write to the same option, so a change in one appears in the other.

**Child themes and code**

- `bookwp_get_option( 'bookwp_font' )` still works; the `bookwp_` prefix is optional. New code should use the bare key, e.g. `bookwp_get_option( 'font' )`.
- `bookwp_get_font_map()` now lives in `inc/options.php` and each entry includes a `label`. Fonts can be added with the `bookwp_font_map` filter.
- New filter `bookwp_color_scheme_choices` for registering extra schemes; add a matching `.color-scheme-<slug>` rule in CSS.
- Templates that call `bookwp_get_adjacent_chapters( $post_id )` now get mode-aware results in both Default and Pro Mode.

**Visual changes child themes may notice**

- `.site-header-inner` and `.site-footer-inner` now use the 720px reading width instead of 960px, so the title, text and footer share one left edge.
- `.chapter-nav-link` is no longer a boxed card: no background, border or radius. Restore them in a child theme if you prefer the old look.
- The `::after` arrow on the current table-of-contents item is removed.
- `.footer-credit` is now styled to match `.footer-copyright`.
- Toggling Pro Mode flushes rewrite rules automatically; you no longer need to tell users to resave permalinks.

---

## From 1.x to 2.0.0

Version 2.0.0 is a full rewrite. Read this guide carefully before upgrading on a live site.

> **Upgrading straight from 1.x to 2.1.0 or later?** Your 1.x settings migrate automatically into the new Theme Options page. Where this section says "Customizer → Book Settings", use **Appearance → Theme Options** instead, and skip the manual permalink step: 2.1.0 refreshes permalinks automatically when Pro Mode changes.

---

### Settings Migration

**Settings have moved to the Customizer.**

The old "Book WP Options" admin page (`Appearance → Book WP Options`) no longer saves settings. It now shows a notice linking to the new Customizer section at `Appearance → Customize → Book Settings`.

Your existing settings are **automatically read** from the legacy `book_wp_*` option keys on first activation of 2.0.0. You do not need to re-enter them manually. To confirm and save them under the new system:

1. Go to **Appearance → Customize → Book Settings**.
2. Review all settings — they should already reflect your previous choices.
3. Click **Publish** to persist them to the Customizer.

After publishing via the Customizer, the legacy option keys are no longer read or written.

**Legacy option key mapping:**

| Legacy key | New Customizer key | Value changes |
|---|---|---|
| `book_wp_font` | `bookwp_font` | `maven` → `maven-pro` |
| `book_wp_color` | `bookwp_color_scheme` | `c_n_b` → `cream-brown`; `b_n_w` → `black-white` |
| `book_wp_comments` | `bookwp_show_comments` | `show` → `1`; `hide` → `0` |
| `book_wp_front_blurb` | `bookwp_front_blurb` | No change |
| `book_wp_chapter_blurb` | `bookwp_chapter_blurb` | No change |
| `book_wp_copyright` | `bookwp_copyright` | No change |

---

### Directory Structure Changes

The theme directory has been significantly reorganised. If you have a child theme, verify your template overrides against the new structure.

```
BEFORE (1.x)                          AFTER (2.0.0)
─────────────────────────────────     ──────────────────────────────────────────
functions.php (all logic)             functions.php (loader only, 32 lines)
                                      inc/
                                      ├── customizer.php
                                      ├── pro-mode.php
                                      ├── setup.php
                                      ├── chapters.php
                                      ├── admin.php
                                      └── structured-data.php

template-parts/ (flat)                template-parts/
├── content.php                       ├── content/
├── content-page.php                  │   ├── content.php
├── content-chapter.php               │   ├── content-chapter.php
├── toc.php                           │   └── content-page.php
├── chapter-navigation.php            ├── navigation/
├── site-branding.php                 │   ├── toc.php
└── metadata.php                      │   └── chapter-navigation.php
                                      └── global/
                                          ├── site-branding.php
                                          └── metadata.php

page-templates/                       templates/
├── page-toc.php                      ├── page-toc.php
├── page-blank.php                    ├── page-blank.php
└── page-landing.php                  └── page-landing.php

(no theme.json)                       theme.json (v3 — Global Styles)
(no languages/)                       languages/
                                      ├── book-wp.pot
                                      ├── fr_FR.po / .mo
                                      └── ms_MY.po / .mo
```

**Child theme impact:**

- If your child theme overrides any template part from `template-parts/`, update the path. Example: `template-parts/toc.php` → `template-parts/navigation/toc.php`.
- If your child theme overrides a page template from `page-templates/`, move the file to `templates/` in your child theme directory.
- If your child theme requires `functions.php` helpers, all functions remain public — they are now defined in `inc/` files loaded by the parent `functions.php`. No changes to function names or signatures.

---

### Template Changes

All template files have been rewritten. If you have a child theme using template overrides from 1.x, review and update them.

**Structural changes affecting CSS or child theme overrides:**

- `header.php`: IE conditional HTML classes removed. `<html>` uses only `language_attributes()`. No inline scripts.
- `header.php`: `<head>` no longer contains a manual `<title>` block — managed by `add_theme_support('title-tag')`.
- `header.php`: `<section id="page-wrap">` is now `<div id="page-wrap" class="site">`.
- `header.php`: `<header id="header">` gains class `site-header`.
- `site-branding.php`: site title is always `<p class="site-title">`. The conditional `<h1>` has been removed — content templates own the `<h1>` on every view.
- `site-branding.php`: primary navigation is now rendered here via `wp_nav_menu()` when a menu is assigned.
- `single.php`: chapter articles use `<article class="chapter-article">`. Inline `<script>` setting `page_type` / `page_title` removed.
- `index.php` / `front-page.php`: `<section id="book_title">` block replaced by `.book-title-section`.
- `sidebar.php`: only renders if the sidebar has active widgets.
- `searchform.php`: search button now uses an inline SVG icon instead of a Unicode emoji.

---

### CSS Class / ID Changes

If your child theme or custom CSS targets any of these selectors, update them:

| Old | New | Notes |
|---|---|---|
| `#page-wrap` (section) | `#page-wrap.site` (div) | Element changed |
| `#header` | `#header.site-header` | Class added |
| `#book_title` | `.book-title-section` | |
| `#front_blurb` | `.front-blurb` | |
| `#chapter_blurb` | `.chapter-blurb` | Inside header |
| `#table_of_contents_header` | `.toc-heading` | Now `<h2>` |
| `#table_of_contents` | `.toc-list` | Now `<ol>` inside `<nav>` |
| `#next_chapter` | `.chapter-navigation .nav-next` | |
| `.chapter_title` | `.chapter-title` | |
| `.entry-content` (section) | `.entry-content` (div) | Element changed |
| `#sidebar` | `#sidebar.sidebar.widget-area` | Classes added |

---

### Removed JavaScript

- `Modernizr` is no longer bundled. The `no-js` body class is no longer added by the theme.
- Inline `page_type` / `page_title` JavaScript variables removed.
- The `bookmark` feature and `#bookmark` element removed.
- jQuery CDN override removed. WordPress ships jQuery — enqueue your scripts with `jquery` as a dependency if needed.

The new `assets/js/book-wp.js` is vanilla JavaScript (no jQuery dependency).

---

### New: Global Styles (theme.json)

2.0.0 ships `theme.json` version 3. This makes Book WP a **hybrid classic theme**.

**What this means:**

- Appearance → Editor → Styles is available for adjusting colours, typography, and spacing visually.
- The block editor colour palette is now the theme's curated 9-colour set. WordPress's default 20+ colours and gradients are suppressed.
- Font family choices in the editor match the 5 fonts available in the Customizer.
- As of 2.1.0 the palette has 12 swatches, adding the Dark scheme colours.

**If you have a child theme with custom colours:** Consider defining your colour overrides in a `theme.json` in your child theme rather than CSS custom property overrides — this ensures they appear in the editor palette.

---

### New: Pro Mode (opt-in)

Pro Mode was added in 2.0.0. If you want to use the Custom Post Type / multi-book features:

1. Enable **Pro Mode** in Customizer → Book Settings.
2. Visit **Settings → Permalinks** and click **Save Changes** to flush rewrite rules.
3. Create **Book** taxonomy terms and assign your chapters to them.

If you do **not** enable Pro Mode, the theme behaves exactly like the old Default Mode (posts in the `chapter` category). No migration is required.

---

### New: Translations

2.0.0 is the first release with complete translation files.

- `languages/book-wp.pot` — master template (86 strings). Open in Poedit to create a new language.
- `languages/fr_FR.po` / `.mo` — French.
- `languages/ms_MY.po` / `.mo` — Malay (Malaysia).

If you previously managed your own translation files under the `html5reset` text domain (the upstream theme Book WP was forked from), those files will not load — the correct text domain is `book-wp`. Rename/regenerate as needed.

---

### Accessibility Changes

2.0.0 introduces WCAG 2.2 AA markup. If you use assistive technologies for development testing:

- A skip link is present at page top. Becomes visible on keyboard focus.
- All `<nav>` elements have `aria-label`.
- Table of Contents uses `<ol>` (ordered list) not `<ul>`.
- Active TOC item has `aria-current="page"`.
- Focus outlines are high-contrast (3px solid).
- Heading structure enforced: one `<h1>` per view.
- External links get a screen-reader "(opens in new tab)" annotation automatically via JavaScript.

If your child theme overrides heading levels, removes landmark elements, or injects colour without sufficient contrast, verify WCAG 2.2 AA compliance is preserved.

---

### Analytics

The legacy Google Analytics snippet has been removed. Re-add analytics via a dedicated plugin or the `wp_head` hook, ensuring compliance with your applicable privacy regulations.

---

## Future Upgrade Template

_When preparing future upgrade guides, copy this section:_

```
## From X.Y to X.Z

### Breaking Changes
- [ Template paths, option keys, hook names, CSS selectors ]

### New Features
- [ Customizer settings, templates, hooks, theme.json additions ]

### Accessibility Notes
- [ Markup changes affecting screen readers, focus order, contrast ]

### Steps
1. Back up your theme (especially child theme overrides).
2. Deactivate, replace theme files, reactivate.
3. Visit Appearance → Theme Options and confirm settings on each tab.
4. If pretty permalinks 404 after toggling Pro Mode: Settings → Permalinks → Save Changes.
5. Test keyboard navigation and screen reader flow.
6. Run contrast checks on any custom colour overrides.
7. Run Lighthouse Accessibility audit — target ≥95.
```
