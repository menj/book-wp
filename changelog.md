# Changelog

All notable changes to Book WP are documented in this file.

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
Versioning: [Semantic Versioning](https://semver.org/)

---

## [2.0.0] — 2026-03-01

### Added

**Architecture**
- `inc/` directory: monolithic `functions.php` (880 lines) split into six focused files — `customizer.php`, `pro-mode.php`, `setup.php`, `chapters.php`, `admin.php`, `structured-data.php`. `functions.php` is now a loader only.
- `theme.json` (schema version 3): curated 9-colour palette, 5 font family declarations matching Customizer options, type scale, spacing tokens, block default styles. Suppresses WP default palette and gradients. Enables Global Styles panel (Appearance → Editor → Styles).
- `template-parts/content/`, `template-parts/navigation/`, `template-parts/global/` subdirectories — template parts reorganised by concern.
- `templates/` directory for PHP page templates (replaces `page-templates/`). Compatible with WP 5.4.1+ subdirectory scanning.
- `assets/img/` directory.
- `index.php` silence guards in all new directories.

**Settings & Customizer**
- Customizer section "Book Settings" replacing the legacy options page (Appearance → Customize → Book Settings).
- Settings: `bookwp_font`, `bookwp_color_scheme`, `bookwp_show_comments`, `bookwp_front_blurb`, `bookwp_chapter_blurb`, `bookwp_copyright`, `bookwp_enable_pro_mode`.
- Legacy option key fallback: existing installations silently read old `book_wp_*` option keys until the user saves via Customizer.

**Pro Mode**
- Opt-in Custom Post Type `chapter` and taxonomy `book` registered only when enabled.
- Book term meta: `book_cover` (attachment ID), `book_description`, `publication_year`.
- Templates: `single-chapter.php`, `archive-chapter.php`, `taxonomy-book.php`.

**Templates & Template Parts**
- `front-page.php` for dedicated front-page rendering.
- `template-parts/content/content.php`, `content-chapter.php`, `content-page.php`.
- `template-parts/navigation/toc.php`, `chapter-navigation.php`.
- `template-parts/global/site-branding.php`, `metadata.php`.
- Page templates: `templates/page-toc.php`, `templates/page-blank.php`, `templates/page-landing.php`.

**Navigation**
- Primary nav menu registered (`primary`) and rendered via `wp_nav_menu()` in `site-branding.php`. Only displays when a menu is assigned. Includes 2-level sub-menu support.
- CSS for `.primary-navigation`, `.primary-nav-list`, and sub-menus.

**Accessibility (WCAG 2.2 AA)**
- Skip link ("Skip to main content") appearing on keyboard focus (WCAG 2.4.1).
- `aria-label` on all `<nav>` elements (WCAG 1.3.1).
- `aria-current="page"` on active TOC item (WCAG 4.1.2).
- Visible high-contrast focus indicator on all interactive elements: 3px solid, `3:1` contrast (WCAG 2.4.7, 2.4.11).
- CSS custom properties for all colour values.
- Cream and Brown colour scheme (WCAG 2.2 AA verified).
- `prefers-reduced-motion` disabling all animations/transitions (WCAG 2.3.3).
- Reflow at 320px viewport width (WCAG 1.4.10).
- Text-spacing resilience (WCAG 1.4.12).
- Descriptive chapter nav links with screen-reader text (WCAG 2.4.4).
- `rel="prev"` / `rel="next"` on chapter nav links.
- External link annotation via JS: "(opens in new tab)" screen-reader text added automatically.
- Touch target minimum 44×44px (WCAG 2.5.8).
- `html5` theme support extended to include `search-form` for accessible WP-generated search markup.

**Standards & Metadata**
- `Update URI: false` in `style.css` (WP 6.1+) — prevents accidental theme replacement from wordpress.org.
- `$content_width` global set to `720` in `bookwp_setup()`.
- `search-form` added to `html5` theme support array.

**Search**
- Search submit button uses inline SVG icon (magnifying glass) in place of Unicode emoji `&#128269;`.

**Structured Data & Hooks**
- Minimal optional JSON-LD (Article schema) on singular views, skipped if Yoast / AIOSEO / Rank Math / SEOPress active.
- `bookwp_before_chapter_content` / `bookwp_after_chapter_content` action hooks.
- `bookwp_toc_query_args` / `bookwp_pro_toc_query_args` filter hooks.
- `bookwp_enable_structured_data` filter to disable JSON-LD.
- `bookwp_get_option()` helper with Customizer + legacy fallback.
- `bookwp_is_pro_mode()` helper.

**Assets & Performance**
- `assets/js/book-wp.js`: vanilla JS — skip link smooth scroll, keyboard `role="button"` support, external link annotation. Respects `prefers-reduced-motion`.
- Conditional asset enqueue: JS only on singular/front-page views; Google Fonts only when non-Georgia font selected.
- `wp_body_open()` call in `header.php`.
- Google Fonts loaded via `wp_enqueue_style()` over HTTPS with `display=swap`.
- `wp-block-styles` and `editor-styles` theme support.

**Translations**
- `languages/book-wp.pot` — master translation template, 86 strings.
- `languages/fr_FR.po` / `fr_FR.mo` — complete French translation (86/86 strings). Correct non-breaking space before French punctuation. Plural forms: `nplurals=2; plural=(n > 1)`.
- `languages/ms_MY.po` / `ms_MY.mo` — complete Malay (Malaysia) translation (86/86 strings). No grammatical plural: `nplurals=1; plural=0`.

**Documentation**
- `readme.txt` (WordPress directory format) with full file structure, accessibility statement, translations section, FAQ.
- `changelog.md` (Keep a Changelog format).
- `upgrading.md` — 1.x → 2.0.0 migration guide with option key mapping table, template/CSS/JS change lists.
- `license.txt` (GPL-2.0-or-later).

### Changed

**Architecture**
- `functions.php` reduced from 880 lines to 32 lines (loader only).
- `page-templates/` renamed to `templates/`. WP 5.4.1+ discovers page templates in any subdirectory.
- `template-parts/` reorganised from 7 flat files into `content/`, `navigation/`, `global/` subdirectories.
- `get_template_part()` calls updated to use `$slug, $name` two-argument form where applicable (`content/content`, `chapter` / `page`), enabling WP's built-in fallback mechanism.
- Load order of `inc/` files is explicit and documented: `customizer.php` first (defines helpers used by others), `pro-mode.php` second (defines `bookwp_is_pro_mode()` used by setup/chapters/admin).

**Settings**
- Settings storage moved from direct `$_POST` saving to WordPress Customizer.
- Legacy options page shows a notice directing users to the Customizer; no longer saves any data.

**Templates**
- `site-branding.php`: removed conditional `<h1>`/`<p>` — site title always uses `<p>`. Content templates own the `<h1>` on every view (fixes WCAG 1.3.1 double-`<h1>` on "Latest Posts" homepage).
- `index.php` loop: was incorrectly using `content-page` template part for all post types; corrected to `content`.
- `footer.php` `printf()`: replaced with `wp_kses( sprintf(...), [...] )` — correct late-escape pattern for PHPCS/Theme Check.
- `toc.php`: now branches on `bookwp_is_pro_mode()` to use correct query function. Previously always called Default Mode query, resulting in empty TOC in Pro Mode.

**Standards**
- Tags in `style.css` updated to valid WP.org directory tags only: removed `books`, `reading` (unofficial), removed `custom-header` (feature not registered). Added `accessibility-ready`, `custom-menu`, `blog`.
- `html5` support array extended: `search-form` added.
- Nav menus label updated: `'Primary Menu'` → `'Primary Navigation'` (more descriptive for screen readers).
- `wp_nav_menu()` added to `site-branding.php` with `has_nav_menu()` guard.

**Code Quality**
- `add_action('init', 'bookwp_head_cleanup')` previously placed before its function definition; moved to correct order (function defined first).

### Removed

**Legacy / Deprecated**
- `automatic_feed_links()` function call → `add_theme_support('automatic-feed-links')`.
- Manual `<title>` tag logic → `add_theme_support('title-tag')`.
- Duplicate `<meta name="title">` in `<head>`.
- Legacy Google Analytics snippet.
- jQuery deregistration and HTTP CDN jQuery override.
- `/_/` legacy asset references (missing paths causing 404s).
- IE conditional HTML classes from `<html>` element.
- Hardcoded `<script>` tags in `header.php`.
- Inline `page_type` / `page_title` JavaScript variables.
- `<meta name="google-site-verification">` placeholder.
- `<meta name="author">` / `<meta name="Copyright">` hardcoded to "Your Name Here".
- Dublin Core meta tags.
- `TEMPLATEPATH` constant usage.
- `screen_icon()` call (deprecated since WP 3.8).
- `add_theme_support('post-formats', ...)`.
- `wp_specialchars()` usage → `esc_html()`.
- `get-theme-options.php` and `the_options_page.php`.
- HTTP Google Fonts URLs.
- Favicon hardcoded to `/_/img/favicon.ico`.
- `custom-header` tag from `style.css` (feature not registered).
- Unicode emoji in search submit button (`&#128269;`) → inline SVG.
- `screenshot-readme.txt` (not a WP standard file).
- Junk directories created by brace-expansion `mkdir` error.

### Fixed

**Queries (Breaking)**
- `bookwp_get_chapter_posts()`: top-level `meta_key` generated an `INNER JOIN` that silently excluded chapter posts with no `chapter_number` meta. Replaced with named `meta_query` clauses (`chapter_num_clause` / `no_chapter_clause`) producing a `LEFT JOIN` — all chapter posts are now included with correct fallback to date ordering.
- `bookwp_get_pro_chapter_posts()`: same `INNER JOIN` issue. Same fix applied.

**Templates (Breaking)**
- `toc.php` always called `bookwp_get_chapter_posts()` regardless of mode, producing an empty TOC in Pro Mode. Now branches on `bookwp_is_pro_mode()`.
- `index.php` loop: `get_template_part('template-parts/content-page')` called for all post types in the loop fallback — wrong template. Corrected to `content`.
- `single.php`: double `<h1>` when site uses "Latest Posts" as homepage (both `site-branding.php` and `front-page.php` emitted `<h1>`). Fixed by removing conditional heading from `site-branding.php`.

**Code Quality**
- `footer.php`: `printf(esc_html__(...), '<a href...>')` passed raw HTML as printf argument without escaping — PHPCS violation. Replaced with `wp_kses(sprintf(...), [...])`.

**fr_FR Translation**
- Stray empty `msgid ""` block with blank `#:` reference caused corrupt PO file. Removed.
- 9 `msgstr` lines missing non-breaking space (U+00A0) before French punctuation colons (`:`) — typographically incorrect per French rules. Fixed in: plural admin notice, customizer descriptions, search results label, search form label, pagination label, chapter navigation labels.
- `POT-Creation-Date` and `PO-Revision-Date` were identical. Corrected.

### Security

- Removed direct `$_POST` save path (no nonce, no capability check, no sanitization).
- All outputs escaped by context: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`.
- All Customizer inputs use sanitize callbacks.
- No HTTP asset URLs remain.
- `Update URI: false` added to `style.css`.
- `ABSPATH` guard on all PHP files.

### Accessibility

Achieves WCAG 2.2 AA for all theme-controlled UI.

- Skip link (2.4.1)
- Focus styles on all interactive elements (2.4.7, 2.4.11)
- Contrast verified for both colour schemes — ≥4.5:1 text, ≥3:1 UI (1.4.3, 1.4.11)
- Reflow at 320px (1.4.10)
- Text spacing resilience (1.4.12)
- `aria-current="page"` on active TOC item (4.1.2)
- Landmark structure: `header`/`nav`/`main`/`footer`/`aside` (1.3.1)
- One `<h1>` per view — double-`<h1>` bug fixed (1.3.1, 2.4.6)
- Descriptive link text (2.4.4)
- External link annotation "(opens in new tab)" (2.4.4)
- `lang` attribute via `language_attributes()` (3.1.1)
- `prefers-reduced-motion` respected (2.3.3)
- Touch target minimum 44×44px (2.5.8)
- Accessible search form via `html5` support `search-form`
- SVG search icon (no OS-dependent emoji rendering)

### Developer

- `bookwp_before_chapter_content` and `bookwp_after_chapter_content` action hooks.
- `bookwp_toc_query_args` and `bookwp_pro_toc_query_args` filter hooks.
- `bookwp_enable_structured_data` filter.
- `bookwp_get_option($key, $default)` — reads Customizer with legacy `book_wp_*` fallback.
- `bookwp_is_pro_mode()` — returns bool.
- `bookwp_get_chapter_posts()` — returns ordered chapter array (Default Mode). Named meta_query clauses ensure posts without `chapter_number` are included.
- `bookwp_get_adjacent_chapters($post_id)` — returns prev/next chapter (Default Mode).
- `bookwp_get_pro_chapter_posts($book_term_id)` — returns ordered chapters for a book (Pro Mode). Named meta_query clauses as above.
- `bookwp_get_font_map()` — returns Google Fonts URL/family map.
- CSS custom properties for all design tokens, enabling clean child-theme overrides.
- `theme.json` `customTemplates` array registers page templates for block editor awareness.

---

## [1.x] — Legacy

Pre-2.0.0 versions of Book WP are considered legacy and are no longer supported. See `upgrading.md` for migration guidance.

---

*Changelog format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)*
