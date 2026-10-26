# Book WP — TODO

Action items from the 2.0.0 codebase review. Items marked done shipped in 2.1.0.

## 1. Correctness (fix first)

- [x] **Remap legacy font value.** `bookwp_get_option()` returns the raw legacy `book_wp_font` value, so upgraded sites that used `maven` get a dead `font-maven` body class and fall back to Georgia. Map `maven` → `maven-pro` in the legacy branch of `inc/customizer.php` (around line 221), as `upgrading.md` already promises.
- ~~Add `comments.php`~~ — intentionally omitted; the theme relies on core's default comments template by design.
- [x] **Flush rewrite rules when Pro Mode is toggled.** Hook `customize_save_after` and call `flush_rewrite_rules()` when `bookwp_enable_pro_mode` changes, removing the manual "visit Settings → Permalinks" step.
- [x] **Limit JSON-LD to chapters in Default Mode.** `bookwp_structured_data()` in `inc/structured-data.php` fires on every single post; it should also check `has_category( 'chapter' )` when Pro Mode is off.
- [x] **Use or remove `bookwp_get_adjacent_chapters()`.** The helper in `inc/chapters.php` is never called; `template-parts/navigation/chapter-navigation.php` re-implements it inline. Prefer calling the helper (extend it to accept a book term for Pro Mode).
- [x] **Make the JS string translatable.** "(opens in new tab)" is hard-coded in `assets/js/book-wp.js`. Pass it via `wp_localize_script()` or `wp_set_script_translations()`.
- [x] **Move the leading space out of the author string.** `' by %s'` in `template-parts/global/metadata.php` is fragile for translators; put the space in the markup.

## 2. Settings UI (user preference: tabbed/sectioned settings, configurable colours)

Shipped as a dedicated **Appearance → Theme Options** page with three tabs plus a mirrored Customizer panel.

- [x] **Restructure the Customizer into a panel with sections.** Replace the single `bookwp_book_settings` section with a `bookwp` panel containing:
  - Typography & Colours — font, colour scheme (+ any custom colour controls)
  - Content & Blurbs — front blurb, chapter blurb, copyright
  - Advanced — Pro Mode, comments toggle
  Keep `autofocus[section]` in `inc/admin.php` pointing at a valid section.
- [x] **Add a third colour scheme** (e.g. a dark / night-reading scheme) wired through the existing CSS custom properties and a body class.
- [x] **Optional: colour pickers.** Add `WP_Customize_Color_Control` fields (background, text, link/accent) that override the scheme's custom properties via an inline style block, so colours are truly variable rather than preset-only.
- [x] **Add `prefers-color-scheme: dark` support** as an opt-in "Auto" scheme choice.

## 3. Fonts

- ~~Add an additional font stack~~ — closed by the owner; no new font will be added.
- ~~Consider self-hosting web fonts~~ — closed with the font item above.

## 4. Maintainability

- [x] **Single source of truth for the version.** `2.0.0` is hard-coded in `inc/setup.php` (twice), `assets/css/book-wp.css`, and `assets/js/book-wp.js`. Define `BOOKWP_VERSION` from `wp_get_theme()->get( 'Version' )` in `functions.php` and use it for enqueues.
- [x] **Cache the missing-`chapter_number` admin check.** `bookwp_admin_notice_chapter_number()` runs an uncached `get_posts` on every post-list and editor load; store the result in a short transient and clear it on `save_post`.
- [x] **Dedicated editor stylesheet.** `add_editor_style( 'assets/css/book-wp.css' )` loads the whole front-end sheet into the editor, leaking `html`/`#page-wrap` rules. Split typography/colour tokens into `assets/css/editor.css`.
- [x] **Remove the empty `* { }` rule** in `assets/css/book-wp.css` (text-spacing section) — it exists only to hold comments.
- [x] **Fix duplicate `align-items` in `.toc-list a`** (`baseline` then `center`); keep one.

## 5. Tooling & repo hygiene

- [x] Add `.gitignore` (OS files, editor files, `node_modules`, `vendor`).
- [x] Add `phpcs.xml.dist` using WordPress Coding Standards and `composer.json` with `wp-coding-standards/wpcs`.
- [x] Add a GitHub Actions workflow running `php -l`, PHPCS, and the WordPress Theme Check plugin.
- [x] Add (or commit) the `.pot` build script referenced in `languages/book-wp.pot` (`X-Generator: Book WP build script`), e.g. a `wp i18n make-pot` npm/composer script.
- [x] Bump `Tested up to` in `style.css` and `readme.txt` to the current WordPress release before the next tag.

## Already verified — no action needed

- JS and CSS are separated into `assets/js/` and `assets/css/`; `style.css` holds only the theme header.
- All colours are CSS custom properties on `:root`, swapped per scheme via a body class.
- All PHP files pass `php -l`; output is escaped by context; all `inc/` and template files carry `ABSPATH` guards.
- French and Malay translations are complete against the current `.pot`.

## 6. Design pass (shipped in 2.1.0)

- [x] Align header and footer to the reading column, including on mobile.
- [x] Style the footer credit to match the copyright line.
- [x] Replace boxed previous/next cards with lighter borderless links.
- [x] Use the interface font for table-of-contents numbers; remove the current-chapter arrow.
- [x] Theme Options: distinct scheme swatches, On/Off switch text, styled Customizer link.

## 7. Closed without action

- ~~Add translations for the new 2.1.0 strings to `fr_FR.po` and `ms_MY.po`~~ — closed by the owner. New strings fall back to English in those locales.

## 8. Open after 2.2.0

From the post-2.1.0 codebase check and the SEO pass.

- [ ] **Replace the Theme Check CI job.** `.github/workflows/lint.yml` calls `anthonyb/theme-check-action`, which could not be verified to exist. Remove the job or run the Theme Check plugin in a WordPress test environment.
- [ ] **Make PHPCS pass.** About 119 errors, mostly unprefixed template variables (`$copyright`, `$chapters`, `$tagline`), file-comment style and alignment. 22 are auto-fixable with `composer phpcs:fix`. Exclude `bin/` in `phpcs.xml.dist`.
- [ ] **Show the chosen font and custom colours in the block editor.** `assets/css/editor.css` relies on `.font-*` body classes the editor never receives. Inject `--font-body` and colour overrides through the `block_editor_settings_all` filter.
- [ ] **Remove the dead template overrides** `$prev_chapter` / `$next_chapter` in `template-parts/navigation/chapter-navigation.php`, or read them from `$args` passed to `get_template_part()`.
- [ ] **Test 2.2.0 in a live WordPress install.** Check the meta description, canonical and JSON-LD output with Google's Rich Results Test, plus the breadcrumbs, the `/book/` redirect and pagination.

### Resolved in 2.2.0

- [x] Structured data HTML-escaped values ("War &amp; Peace").
- [x] Wrong SEOPress detection constant.
- [x] Nested `<nav>` on search results.
- [x] Landing page "Read Now" button styling and generic anchor text.

### Known and accepted

- French and Malay sites show English for strings added or changed since 2.1.0, including "by %s". Translations were closed by the owner.
