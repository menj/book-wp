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

- [ ] **Add the additional font stack requested by the owner.** (Still blocked: stack not yet provided.) _Font stack not yet provided — paste family names, fallback chain, source (Google Fonts / self-hosted / system), weights, and whether it is a new choice or the new default._ When adding a font, update all of:
  - `bookwp_get_font_map()` and `bookwp_sanitize_font()` in `inc/customizer.php`
  - the `choices` array of the `bookwp_font` control
  - the `.font-*` body-class rules in `assets/css/book-wp.css`
  - `settings.typography.fontFamilies` in `theme.json`
  - `languages/book-wp.pot` (new label string)
- [ ] **Consider self-hosting web fonts** under `assets/fonts/` with `@font-face` to avoid the third-party Google Fonts request (privacy/GDPR and offline reliability).

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
