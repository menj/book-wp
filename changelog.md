# Changelog

All notable changes to Book WP are documented in this file.

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
Versioning: [Semantic Versioning](https://semver.org/)

---

## [2.2.1] — 2026-10-26

### Fixed
- Block editor now shows the chosen reading font and custom colours, passed as CSS custom properties through `block_editor_settings_all` instead of body classes the editor never receives.
- Chapter navigation overrides now work through `get_template_part()`'s `$args` (`prev`, `next`). The previous `$prev_chapter` / `$next_chapter` variables were never set by WordPress.
- Code passes WordPress Coding Standards with no errors or warnings: template variables prefixed `bookwp_`, file doc comments added, short ternary removed, `bookwp_get_option()` second parameter renamed to `$fallback`.

### Changed
- CI: removed the Theme Check job, which referenced an unverified GitHub Action. `bin/` is excluded from PHPCS.

---

## [2.2.0] — 2026-10-26

Search engine optimisation pass against Google's SEO Starter Guide and Google's list of supported structured data.

### Added
- `inc/seo.php`: description meta tag on every indexable view, built from page-specific text only.
- Canonical URLs for listing views (front page, posts page, books, chapter archive, categories, tags, authors), without query strings.
- Visible breadcrumbs on chapters (`bookwp_breadcrumbs()`), with a `bookwp_breadcrumbs` filter.
- `BreadcrumbList` JSON-LD matching the visible breadcrumbs.
- `BlogPosting` JSON-LD on regular posts.
- Article markup now includes author (name and URL), image, `mainEntityOfPage` and, in Pro Mode, `isPartOf` the parent Book.
- Visible byline (date and linked author) and featured image on chapters, so every structured data property describes visible content.
- `template-parts/global/featured-image.php` with an alt-text fallback.
- Archive and blog listings get an `<h1>` heading, an optional description, and pagination.
- 404 page lists the table of contents.
- `/book/` with no book slug redirects to the front page.
- Detection of Rank Math (constant), SEOPress, The SEO Framework and Slim SEO via `bookwp_seo_plugin_active()`.

### Changed
- Book Landing Page template has a single `<h1>`; its button now links to the first chapter with the text "Start reading: {chapter}".
- In Pro Mode the chapter back link goes to the book's contents page and names the book.
- Chapter navigation titles use the link colour so they are recognisable as links.
- Book covers fall back to "Cover of {book}" alt text.

### Fixed
- JSON-LD values were HTML-escaped, so "War & Peace" was published as "War &amp;amp; Peace". Values are now plain text, encoded safely for a script block.
- SEOPress was detected by the wrong constant (`SLP_VERSION`), so the theme duplicated its markup.
- Search results had a `<nav>` nested inside another `<nav>`.
- Structured data no longer outputs on password-protected posts.

---

## [2.1.0] — 2026-10-26

### Added

**Settings**
- Tabbed **Theme Options** page at Appearance → Theme Options with three tabs: Typography & Colours, Content & Blurbs, Advanced. Built on the Settings API; keeps the 1.x `book-wp-options` menu slug so old bookmarks work.
- Dedicated admin assets: `assets/css/admin.css` and `assets/js/admin.js` (accessible WAI-ARIA tabs with arrow-key navigation, WordPress colour pickers, unsaved-changes hint). Loaded only on the Theme Options screen.
- Customizer restructured into a **Book WP panel** with sections matching the three tabs. Customizer controls bind to the same `bookwp_options` array, so both surfaces stay in sync.
- Schema-driven options layer (`inc/options.php`): a single `bookwp_get_option_schema()` defines each field's tab, type, default, sanitizer and labels; the Theme Options page, Customizer, and sanitization all derive from it.
- One-time migration seeds `bookwp_options` from 2.0.x theme_mods and 1.x `book_wp_*` options on first admin load.

**Colour**
- **Dark** colour scheme (night reading) and **Auto** scheme that follows `prefers-color-scheme`.
- Optional **custom colours** (background, text, accent) overriding the active scheme via CSS custom properties; emitted as a small inline style.
- `<meta name="color-scheme">` output for Dark and Auto so native form controls match.

**Tooling**
- `BOOKWP_VERSION` constant read from the `style.css` header; used for all asset versioning.
- `bin/make-pot.php` regenerates `languages/book-wp.pot` (with `--check` mode for CI).
- `composer.json`, `phpcs.xml.dist` (WordPress Coding Standards + PHPCompatibilityWP), `.gitignore`.
- GitHub Actions workflow: PHP syntax check on 7.4/8.1/8.3, PHPCS, Theme Check, `.pot` freshness.
- `assets/css/editor.css`: dedicated block editor stylesheet with typography and colour tokens only.

### Changed
- Pro Mode toggle now **flushes rewrite rules automatically** on the next request after saving (from either Theme Options or Customizer). The manual Settings → Permalinks step is no longer required.
- Chapter prev/next navigation uses `bookwp_get_adjacent_chapters()`, now mode-aware via the new `bookwp_get_chapter_sequence()`, instead of duplicating the logic in the template part.
- The "missing `chapter_number`" admin notice count is cached in a transient for an hour and invalidated on post/meta/term changes.
- JSON-LD Article markup in Default Mode is emitted only for posts in the `chapter` category, not every single post.
- The "(opens in new tab)" screen-reader text is now translatable, passed to JS via `wp_localize_script()`.
- Skip link and search button colours use scheme tokens instead of hard-coded white, so they remain legible in Dark mode.
- Requires at least WordPress 6.2; tested up to 6.8.

### Design
- Header and footer align to the reading column; footer credit styled to match the copyright line.
- Lighter, borderless previous/next chapter links with titles in the body font.
- Table of contents numbers use the interface font; the redundant current-chapter arrow is removed.
- Theme Options: scheme swatches show each scheme background with its accent colour, switches show On/Off instead of repeating the label, and the Customizer link is a proper secondary button with hover and focus states.

### Fixed
- Legacy 1.x font value `maven` is remapped to `maven-pro`; upgraded sites no longer fall back to Georgia with a dead body class.
- Duplicate `align-items` declaration on `.toc-list a`.
- Empty `* {}` rule removed from the stylesheet.
- Leading space removed from the translatable author string in `metadata.php`.

### Removed
- Legacy "Book WP Options" redirect page (replaced by the real Theme Options page at the same slug).
- `inc/customizer.php` option helper and font map (moved to `inc/options.php`).

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
