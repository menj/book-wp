#!/usr/bin/env php
<?php
/**
 * Build languages/book-wp.pot from the theme's PHP sources.
 *
 * Usage:
 *   php bin/make-pot.php          Regenerate the .pot file.
 *   php bin/make-pot.php --check  Exit non-zero if the .pot file is out of date.
 *
 * This is a dependency-free extractor for the gettext calls used in this theme.
 * If WP-CLI is available, `wp i18n make-pot . languages/book-wp.pot` produces
 * an equivalent file and may be used instead.
 *
 * @package book-wp
 */

$root   = dirname( __DIR__ );
$output = $root . '/languages/book-wp.pot';
$check  = in_array( '--check', $argv, true );

$header = file_get_contents( $root . '/style.css' );
preg_match( '/^Version:\s*(.+)$/mi', $header, $m );
$version = isset( $m[1] ) ? trim( $m[1] ) : '0.0.0';

$files = array();
$iter  = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
foreach ( $iter as $file ) {
	$path = $file->getPathname();
	if ( 'php' !== $file->getExtension() ) {
		continue;
	}
	if ( preg_match( '#/(vendor|node_modules|\.git|bin)/#', $path ) ) {
		continue;
	}
	$files[] = $path;
}
sort( $files );

$str = '(?:"((?:[^"\\\\]|\\\\.)*)"|\'((?:[^\'\\\\]|\\\\.)*)\')';
$ws  = '\s*';

$patterns = array(
	// __( 'text', 'domain' ) and friends.
	array( 'regex' => '/\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(' . $ws . $str . $ws . ',' . $ws . '[\'"]book-wp[\'"]/', 'map' => array( 'msgid' => 1 ) ),
	// _x( 'text', 'context', 'domain' ).
	array( 'regex' => '/\b(?:_x|esc_html_x|esc_attr_x)\(' . $ws . $str . $ws . ',' . $ws . $str . $ws . ',' . $ws . '[\'"]book-wp[\'"]/', 'map' => array( 'msgid' => 1, 'msgctxt' => 2 ) ),
	// _n( 'single', 'plural', $n, 'domain' ).
	array( 'regex' => '/\b_n\(' . $ws . $str . $ws . ',' . $ws . $str . $ws . ',[^,]+,' . $ws . '[\'"]book-wp[\'"]/', 'map' => array( 'msgid' => 1, 'msgid_plural' => 2 ) ),
);

/**
 * Turn a matched PHP string literal into its runtime value.
 */
function bookwp_pot_unquote( $double, $single ) {
	if ( null !== $double && '' !== $double ) {
		return stripcslashes( $double );
	}
	return str_replace( array( "\\'", '\\\\' ), array( "'", '\\' ), (string) $single );
}

/**
 * Escape a string for a .pot msgid.
 */
function bookwp_pot_escape( $s ) {
	return addcslashes( $s, "\"\\\n" );
}

$entries = array();
foreach ( $files as $path ) {
	$src   = file_get_contents( $path );
	$rel   = ltrim( str_replace( $root, '', $path ), '/' );
	$lines = explode( "\n", $src );

	foreach ( $patterns as $p ) {
		if ( ! preg_match_all( $p['regex'], $src, $all, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
			continue;
		}
		foreach ( $all as $match ) {
			$offset = $match[0][1];
			$line   = substr_count( substr( $src, 0, $offset ), "\n" ) + 1;

			$get = function ( $slot ) use ( $match ) {
				$d = isset( $match[ $slot * 2 - 1 ] ) && -1 !== $match[ $slot * 2 - 1 ][1] ? $match[ $slot * 2 - 1 ][0] : null;
				$s = isset( $match[ $slot * 2 ] ) && -1 !== $match[ $slot * 2 ][1] ? $match[ $slot * 2 ][0] : null;
				return bookwp_pot_unquote( $d, $s );
			};

			$e = array( 'msgid' => $get( $p['map']['msgid'] ), 'msgctxt' => null, 'msgid_plural' => null, 'refs' => array(), 'comment' => null );
			if ( isset( $p['map']['msgctxt'] ) ) {
				$e['msgctxt'] = $get( $p['map']['msgctxt'] );
			}
			if ( isset( $p['map']['msgid_plural'] ) ) {
				$e['msgid_plural'] = $get( $p['map']['msgid_plural'] );
			}

			// Translator comment on the preceding non-empty line(s).
			for ( $i = $line - 2; $i >= 0 && $i >= $line - 4; $i-- ) {
				if ( preg_match( '#/\*\s*translators:\s*(.+?)\s*\*/#i', $lines[ $i ], $cm ) ) {
					$e['comment'] = 'translators: ' . $cm[1];
					break;
				}
				if ( '' !== trim( $lines[ $i ] ) && ! preg_match( '#^\s*(/\*|\*|//)#', $lines[ $i ] ) ) {
					break;
				}
			}

			$key = ( $e['msgctxt'] ? $e['msgctxt'] . "\x04" : '' ) . $e['msgid'];
			if ( ! isset( $entries[ $key ] ) ) {
				$entries[ $key ] = $e;
			}
			$entries[ $key ]['refs'][] = $rel . ':' . $line;
			if ( $e['comment'] && empty( $entries[ $key ]['comment'] ) ) {
				$entries[ $key ]['comment'] = $e['comment'];
			}
		}
	}
}

ksort( $entries, SORT_STRING );

$out  = "# Copyright (C) " . gmdate( 'Y' ) . " MENJ\n";
$out .= "# This file is distributed under the GPL-2.0-or-later.\n";
$out .= "msgid \"\"\n";
$out .= "msgstr \"\"\n";
$out .= "\"Project-Id-Version: Book WP {$version}\\n\"\n";
$out .= "\"Report-Msgid-Bugs-To: https://github.com/menj/book-wp/issues\\n\"\n";
$out .= "\"MIME-Version: 1.0\\n\"\n";
$out .= "\"Content-Type: text/plain; charset=UTF-8\\n\"\n";
$out .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
$out .= "\"POT-Creation-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n";
$out .= "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n";
$out .= "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"\n";
$out .= "\"Language-Team: LANGUAGE <LL@li.org>\\n\"\n";
$out .= "\"Plural-Forms: nplurals=2; plural=(n != 1);\\n\"\n";
$out .= "\"X-Generator: bin/make-pot.php\\n\"\n";
$out .= "\"X-Domain: book-wp\\n\"\n\n";

foreach ( $entries as $e ) {
	if ( $e['comment'] ) {
		$out .= '#. ' . $e['comment'] . "\n";
	}
	$out .= '#: ' . implode( ' ', array_unique( $e['refs'] ) ) . "\n";
	if ( null !== $e['msgctxt'] ) {
		$out .= 'msgctxt "' . bookwp_pot_escape( $e['msgctxt'] ) . "\"\n";
	}
	$out .= 'msgid "' . bookwp_pot_escape( $e['msgid'] ) . "\"\n";
	if ( null !== $e['msgid_plural'] ) {
		$out .= 'msgid_plural "' . bookwp_pot_escape( $e['msgid_plural'] ) . "\"\n";
		$out .= "msgstr[0] \"\"\nmsgstr[1] \"\"\n\n";
	} else {
		$out .= "msgstr \"\"\n\n";
	}
}

if ( $check ) {
	$current = file_exists( $output ) ? file_get_contents( $output ) : '';
	// Ignore the copyright year line when comparing.
	$strip = function ( $s ) {
		return preg_replace( '/^# Copyright \(C\) \d{4}/m', '# Copyright (C) YEAR', $s );
	};
	if ( $strip( $current ) !== $strip( $out ) ) {
		fwrite( STDERR, "languages/book-wp.pot is out of date. Run: php bin/make-pot.php\n" );
		exit( 1 );
	}
	echo "languages/book-wp.pot is up to date.\n";
	exit( 0 );
}

file_put_contents( $output, $out );
printf( "Wrote %d strings to %s\n", count( $entries ), str_replace( $root . '/', '', $output ) );
