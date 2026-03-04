=== Book WP ===
Contributors: menj
Tags: one-column, custom-colors, custom-menu, translation-ready, accessibility-ready, blog
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A clean, accessible WordPress theme for publishing online books with chapter ordering, table of contents, and WCAG 2.2 AA compliance.

== Description ==

Book WP is a WordPress theme designed for authors and publishers who want to share their book, serial fiction, or long-form written work online.

**Default Mode** requires no plugin installation. Chapters are ordinary WordPress posts assigned to the "chapter" category. A numeric `chapter_number` custom field controls ordering. The front page automatically renders a Table of Contents.

**Pro Mode** (opt-in via Customizer) registers a dedicated `chapter` Custom Post Type and a `book` taxonomy, enabling multiple books, cleaner URLs, and `menu_order`-based chapter sequencing.

All settings are managed through the Customizer under Appearance → Customize → Book Settings.

**Hybrid Theme / Global Styles:** Book WP ships `theme.json` (version 3), making it a hybrid classic theme. While full Site Editor template editing requires a block theme, the Appearance → Editor → Styles panel is available for adjusting colours, typography, and spacing through a visual interface without editing code.

== Installation ==

1. Upload the `book-wp` folder to your `/wp-content/themes/` directory, or install via Appearance → Themes → Add New.
2. Activate the theme under Appearance → Themes.
3. Visit Appearance → Customize → Book Settings to configure fonts, color scheme, blurbs, and copyright.
4. In **Default Mode**: Create posts, assign them to the "chapter" category, and add a `chapter_number` custom field (integer) to each for deterministic ordering.
5. In **Pro Mode**: Enable "Pro Mode" in the Customizer, then visit Settings → Permalinks to flush rewrite rules. Create Book terms and assign Chapter posts to them.

== Usage ==

=== Default Mode ===

- Create WordPress posts and assign them to the "chapter" category (slug: `chapter`).
- Add a custom field `chapter_number` (integer) to each chapter post. Chapters are ordered by this value ascending. If missing, ordering falls back to publish date.
- The front page renders your Table of Contents automatically.
- The site title and description form the book header.
- Configure a front-page blurb and chapter header blurb under Customizer → Book Settings.

=== Pro Mode ===

Pro Mode activates a Custom Post Type `chapter` and a hierarchical taxonomy `book`.

1. Enable "Pro Mode" in Customizer → Book Settings.
2. Flush rewrite rules: Settings → Permalinks → Save Changes.
3. Create Book terms under Posts → Books.
4. Create Chapter posts under Chapters, assigned to a Book term.
5. Use the "Order" field (Page Attributes) on each chapter to set `menu_order`. Fallback: `chapter_number` meta, then date.
6. Each Book term has a landing page listing its chapters at `/book/{slug}/`.

=== Fonts ===

Choose from: Georgia (default, no external request), Vollkorn, Arvo, Maven Pro, or Ubuntu. Non-Georgia fonts are loaded from Google Fonts over HTTPS with `display=swap`.

=== Color Schemes ===

- **Black and White**: Clean, high-contrast default.
- **Cream and Brown**: Warm, parchment-inspired.

Both schemes meet WCAG 2.2 AA contrast requirements (4.5:1 for body text, 3:1 for UI components).

=== Global Styles (theme.json) ===

Book WP ships `theme.json` v3 defining a curated colour palette (9 swatches), font family declarations matching the Customizer options, a type scale, and spacing tokens. WP's default 20+ colours and gradients are suppressed in favour of the theme palette. Access via Appearance → Editor → Styles.

== Accessibility (WCAG 2.2 AA) ==

Book WP targets WCAG 2.2 Level AA for all theme-controlled UI.

**Keyboard navigation**: All interactive elements are reachable and operable by keyboard alone. No keyboard traps. Focus order follows visual/logical reading order.

**Skip link**: A "Skip to main content" link is provided at the top of every page. It becomes visible on focus.

**Focus styles**: All focusable elements display a high-contrast visible focus indicator (3px solid outline, minimum 3:1 contrast with surrounding colour).

**Landmarks**: Every page uses `<header>`, `<nav>`, `<main>`, `<footer>`, and `<aside>` HTML landmarks. Navigation regions use `aria-label` to distinguish them.

**Headings**: One `<h1>` per view. Heading hierarchy is logical throughout all templates.

**Colour contrast**: Text meets 4.5:1 against background in all colour schemes. Large text and UI components meet 3:1.

**Colour independence**: Information is never conveyed by colour alone.

**Reflow (320px)**: The theme reflows correctly at 320px viewport width with no horizontal scrolling.

**Text spacing**: Layout is resilient to user-applied text spacing overrides (WCAG 1.4.12).

**Reduced motion**: Animations and transitions respect `prefers-reduced-motion`.

**Accessible names**: All controls have accessible names. TOC current item uses `aria-current="page"`. External links include screen-reader "(opens in new tab)" annotation.

**Link text**: All links have descriptive text meaningful out of context. SVG icons include screen-reader text equivalents.

**Language attribute**: `lang` is set via `language_attributes()`.

**Author responsibility**: For post content, authors must provide meaningful alt text for informative images and use descriptive link text. The theme provides accessible defaults and does not introduce barriers to user-authored content.

== Translations ==

Book WP is fully translation-ready. All display strings use the `book-wp` text domain.

**Bundled languages:**

- English (default source)
- French — `fr_FR.po` / `fr_FR.mo`
- Malay (Malaysia) — `ms_MY.po` / `ms_MY.mo`

To add a new language, open `languages/book-wp.pot` in Poedit or Loco Translate, translate all strings, and save as `{locale}.po`. Compile to `{locale}.mo` before deploying.

WordPress 6.5+ also auto-loads translation files placed in `wp-content/languages/themes/book-wp-{locale}.mo`.

== Security ==

- All settings saved only through the WordPress Customizer (no direct `$_POST` saving).
- All output escaped by context (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`).
- All inputs sanitized on save (`sanitize_text_field`, `absint`, `wp_kses_post`).
- Nonce verification handled by the Customizer API.
- `ABSPATH` guard on all PHP files.
- No HTTP asset URLs. No external CDN jQuery override.
- No tracking or analytics code embedded by default.
- `Update URI: false` prevents accidental replacement from wordpress.org.

== File Structure ==

```
book-wp/
├── style.css                        Theme headers (no styles — see assets/css/)
├── theme.json                       Global Styles / block editor palette (v3)
├── functions.php                    Loader only — requires inc/ files
├── inc/
│   ├── customizer.php               Customizer settings, option helper, font map, body class
│   ├── pro-mode.php                 Chapter CPT, Book taxonomy, term meta, rewrite flush
│   ├── setup.php                    Theme supports, asset enqueue, sidebar
│   ├── chapters.php                 Chapter query helpers (Default + Pro Mode)
│   ├── admin.php                    Admin notices, legacy options page redirect
│   └── structured-data.php         JSON-LD, extensibility hooks, head cleanup
├── header.php
├── footer.php
├── index.php
├── front-page.php
├── single.php
├── single-chapter.php               Pro Mode
├── archive-chapter.php              Pro Mode
├── taxonomy-book.php                Pro Mode
├── page.php
├── search.php
├── 404.php
├── sidebar.php
├── searchform.php
├── template-parts/
│   ├── content/
│   │   ├── content.php
│   │   ├── content-chapter.php
│   │   └── content-page.php
│   ├── navigation/
│   │   ├── toc.php
│   │   └── chapter-navigation.php
│   └── global/
│       ├── site-branding.php
│       └── metadata.php
├── templates/                       PHP page templates (Template Name: header)
│   ├── page-toc.php
│   ├── page-blank.php
│   └── page-landing.php
├── assets/
│   ├── css/book-wp.css
│   ├── js/book-wp.js
│   └── img/
├── languages/
│   ├── book-wp.pot                  Master translation template
│   ├── fr_FR.po / fr_FR.mo          French
│   └── ms_MY.po / ms_MY.mo          Malay (Malaysia)
├── readme.txt
├── changelog.md
├── upgrading.md
└── license.txt
```

== Frequently Asked Questions ==

= How do I order my chapters? =
Add a custom field named `chapter_number` (integer) to each chapter post. Chapters are sorted by this value ascending. In Pro Mode, `menu_order` (Page Attributes → Order) takes precedence, with `chapter_number` as fallback.

= What if I don't use chapter_number? =
Chapters fall back to publish date order. An admin notice will appear suggesting you add `chapter_number` for deterministic ordering.

= Can I use this for multiple books? =
Yes. Enable Pro Mode in the Customizer. Pro Mode adds a `book` taxonomy so you can group chapters under separate books, each with its own landing page.

= Can I edit the theme layout visually? =
Book WP is a hybrid classic theme. Full template editing (Site Editor canvas) requires a block theme. However, Appearance → Editor → Styles is available for adjusting Global Styles (colours, typography, spacing) through a visual interface.

= Does this theme work with page builders? =
Basic compatibility is maintained, but the theme is designed for the standard WordPress block editor (Gutenberg) and classic editor.

= Is there analytics tracking? =
No. The theme contains no tracking or analytics code. Any analytics must be added separately as opt-in.

= How do I add a new translation? =
Open `languages/book-wp.pot` in Poedit, translate all strings, and save as `{locale}.po` in the same directory. Compile to `.mo`. Alternatively, use the Loco Translate plugin to manage translations from within WordPress.

== Changelog ==

See changelog.md for the full changelog.

== Upgrade Notice ==

= 2.0.0 =
Major rewrite. Settings have moved from the old Theme Options page to the Customizer (Appearance → Customize → Book Settings). Directory structure reorganised: template parts are now in subdirectories; logic is split across inc/ files. See upgrading.md for full details.
