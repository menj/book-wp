<?php
/**
 * Search Engine Optimisation.
 *
 * Implements the theme-level parts of Google's SEO Starter Guide:
 * - a unique description meta tag on every indexable view,
 * - one canonical URL per document on listing views (WordPress core already
 *   prints rel="canonical" on singular views),
 * - breadcrumb navigation (visible, and mirrored in BreadcrumbList JSON-LD),
 * - a sensible response when a user trims the last segment off a book URL.
 *
 * Everything here steps aside when a dedicated SEO plugin is active, so a site
 * never ends up with two description or canonical tags.
 *
 * @package book-wp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ============================================================
// SEO PLUGIN DETECTION
// ============================================================

/**
 * Whether a dedicated SEO plugin is handling meta tags and structured data.
 *
 * @return bool
 */
function bookwp_seo_plugin_active() {
	$active = defined( 'WPSEO_VERSION' )          // Yoast SEO.
		|| defined( 'AIOSEO_VERSION' )            // All in One SEO.
		|| defined( 'RANK_MATH_VERSION' )         // Rank Math.
		|| class_exists( 'RankMath' )             // Rank Math (older builds).
		|| defined( 'SEOPRESS_VERSION' )          // SEOPress.
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' ) // The SEO Framework.
		|| defined( 'SLIM_SEO_VER' );             // Slim SEO.

	/**
	 * Filter whether the theme should treat an SEO plugin as active.
	 *
	 * @param bool $active True when a known SEO plugin is detected.
	 */
	return (bool) apply_filters( 'bookwp_seo_plugin_active', $active );
}

// ============================================================
// DESCRIPTION META TAG
// ============================================================

/**
 * Reduce HTML or block content to a plain-text summary of about $length characters,
 * cut at a word boundary.
 *
 * @param string $text   Raw content.
 * @param int    $length Target character length.
 * @return string
 */
function bookwp_plain_summary( $text, $length = 155 ) {
	$text = strip_shortcodes( (string) $text );
	$text = excerpt_remove_blocks( $text );
	$text = wp_strip_all_tags( $text, true );
	$text = html_entity_decode( $text, ENT_QUOTES, get_bloginfo( 'charset' ) );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );

	if ( '' === $text || mb_strlen( $text ) <= $length ) {
		return $text;
	}

	$cut   = mb_substr( $text, 0, $length + 1 );
	$space = mb_strrpos( $cut, ' ' );
	if ( false !== $space && $space > $length * 0.6 ) {
		$cut = mb_substr( $cut, 0, $space );
	} else {
		$cut = mb_substr( $cut, 0, $length );
	}
	return rtrim( $cut, " \t.,;:–—-" ) . '…';
}

/**
 * Build the description for the current view, or an empty string when none
 * would be specific to this page (Google advises against generic descriptions).
 *
 * @return string
 */
function bookwp_get_meta_description() {
	$description = '';

	if ( is_front_page() && ! is_singular() ) {
		$blurb       = bookwp_get_option( 'front_blurb' );
		$description = $blurb ? $blurb : get_bloginfo( 'description' );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post && ! post_password_required( $post ) ) {
			$description = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
		}
		if ( is_front_page() && '' === trim( wp_strip_all_tags( $description ) ) ) {
			$blurb       = bookwp_get_option( 'front_blurb' );
			$description = $blurb ? $blurb : get_bloginfo( 'description' );
		}
	} elseif ( is_tax( 'book' ) ) {
		$term        = get_queried_object();
		$description = (string) get_term_meta( $term->term_id, 'book_description', true );
		if ( '' === $description ) {
			$description = term_description( $term );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	} elseif ( is_author() ) {
		$description = get_the_author_meta( 'description', (int) get_query_var( 'author' ) );
	}

	$description = bookwp_plain_summary( $description );

	/**
	 * Filter the description meta tag content for the current view.
	 *
	 * @param string $description Plain-text description. Empty to print nothing.
	 */
	return (string) apply_filters( 'bookwp_meta_description', $description );
}

/**
 * Print the description meta tag.
 *
 * Search results and 404 pages get none: WordPress marks them noindex.
 */
function bookwp_meta_description() {
	if ( bookwp_seo_plugin_active() || is_search() || is_404() ) {
		return;
	}
	$description = bookwp_get_meta_description();
	if ( '' !== $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'bookwp_meta_description', 1 );

// ============================================================
// CANONICAL URLS FOR LISTING VIEWS
// ============================================================

/**
 * Canonical URL for listing views. WordPress core already handles singular
 * views via rel_canonical(), so this only covers the front page (when it lists
 * posts), the posts page, and taxonomy / post-type archives.
 *
 * Query strings (tracking parameters, sort orders) are deliberately dropped so
 * that every listing has exactly one URL.
 *
 * @return string Empty when not applicable.
 */
function bookwp_get_listing_canonical() {
	$base = '';

	if ( is_front_page() && ! is_singular() ) {
		$base = home_url( '/' );
	} elseif ( is_home() ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );
		$base           = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		$base = is_wp_error( $link ) ? '' : $link;
	} elseif ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		$link = get_post_type_archive_link( $post_type ? (string) $post_type : 'post' );
		$base = $link ? $link : '';
	} elseif ( is_author() ) {
		$base = get_author_posts_url( (int) get_query_var( 'author' ) );
	}

	if ( ! $base ) {
		return '';
	}

	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( $paged > 1 ) {
		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) {
			$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/' . $paged, 'paged' );
		} else {
			$base = add_query_arg( 'paged', $paged, $base );
		}
	}

	return $base;
}

/**
 * Print rel="canonical" on listing views.
 */
function bookwp_listing_canonical() {
	if ( bookwp_seo_plugin_active() || is_singular() || is_search() || is_404() ) {
		return;
	}
	$url = bookwp_get_listing_canonical();
	if ( $url ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'bookwp_listing_canonical', 2 );

// ============================================================
// BREADCRUMBS
// ============================================================

/**
 * Return the breadcrumb trail for the current chapter view.
 *
 * Default Mode: Home → Chapter. Pro Mode: Home → Book → Chapter.
 * The last item has no URL because it is the current page.
 *
 * @return array<int, array{name: string, url: string}>
 */
function bookwp_get_breadcrumbs() {
	if ( ! bookwp_is_chapter_view() ) {
		return array();
	}

	$post  = get_queried_object();
	$items = array(
		array(
			'name' => get_bloginfo( 'name' ),
			'url'  => home_url( '/' ),
		),
	);

	if ( bookwp_is_pro_mode() ) {
		$terms = get_the_terms( $post, 'book' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$book = reset( $terms );
			$link = get_term_link( $book );
			if ( ! is_wp_error( $link ) ) {
				$items[] = array(
					'name' => $book->name,
					'url'  => $link,
				);
			}
		}
	}

	$items[] = array(
		'name' => wp_strip_all_tags( get_the_title( $post ) ),
		'url'  => '',
	);

	/**
	 * Filter the breadcrumb trail. Visible breadcrumbs and BreadcrumbList
	 * structured data both read from this, so they always match.
	 *
	 * @param array   $items Breadcrumb items (name, url).
	 * @param WP_Post $post  Current chapter.
	 */
	return apply_filters( 'bookwp_breadcrumbs', $items, $post );
}

/**
 * Whether the current request is a single chapter (either mode).
 *
 * @return bool
 */
function bookwp_is_chapter_view() {
	if ( bookwp_is_pro_mode() ) {
		return is_singular( 'chapter' );
	}
	return is_singular( 'post' ) && has_category( 'chapter', get_queried_object() );
}

/**
 * Render the visible breadcrumb trail.
 */
function bookwp_breadcrumbs() {
	$items = bookwp_get_breadcrumbs();
	if ( count( $items ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'book-wp' ); ?>">
		<ol class="breadcrumbs__list">
			<?php foreach ( $items as $item ) : ?>
				<li class="breadcrumbs__item">
					<?php if ( $item['url'] ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['name'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $item['name'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

// ============================================================
// TRIMMED-URL HANDLING
// ============================================================

/**
 * Google's guide asks sites to anticipate visitors deleting the last part of a
 * URL. /chapter/ is a real archive, but /book/ (the taxonomy base with no term)
 * would 404. Send it to the front page, which lists every book.
 */
function bookwp_redirect_bare_taxonomy_base() {
	if ( ! is_404() || ! bookwp_is_pro_mode() ) {
		return;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$rel  = trim( substr( $path, strlen( rtrim( $home, '/' ) ) ), '/' );

	if ( 'book' === $rel ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'bookwp_redirect_bare_taxonomy_base' );
