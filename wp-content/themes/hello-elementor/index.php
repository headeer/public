<?php
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package HelloElementor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$is_elementor_theme_exist = function_exists( 'elementor_theme_do_location' );
$wrap_main_landmark = static function ( $content ) {
	$content = trim( (string) $content );

	if ( '' === $content ) {
		return '';
	}

	if (
		false !== stripos( $content, '<main' ) ||
		false !== stripos( $content, 'role="main"' ) ||
		false !== stripos( $content, "role='main'" )
	) {
		return $content;
	}

	return '<main id="content" class="site-main" tabindex="-1">' . $content . '</main>';
};

if ( is_singular() ) {
	$single_content = '';
	$has_single_location = false;

	if ( $is_elementor_theme_exist ) {
		ob_start();
		$has_single_location = elementor_theme_do_location( 'single' );
		$single_content = trim( ob_get_clean() );
	}

	if ( $has_single_location ) {
		echo $wrap_main_landmark( $single_content );
	} else {
		get_template_part( 'template-parts/single' );
	}
} elseif ( is_archive() || is_home() ) {
	$archive_content = '';
	$has_archive_location = false;

	if ( $is_elementor_theme_exist ) {
		ob_start();
		$has_archive_location = elementor_theme_do_location( 'archive' );
		$archive_content = trim( ob_get_clean() );
	}

	if ( $has_archive_location ) {
		echo $wrap_main_landmark( $archive_content );
	} else {
		get_template_part( 'template-parts/archive' );
	}
} elseif ( is_search() ) {
	$search_content = '';
	$has_search_location = false;

	if ( $is_elementor_theme_exist ) {
		ob_start();
		$has_search_location = elementor_theme_do_location( 'archive' );
		$search_content = trim( ob_get_clean() );
	}

	if ( $has_search_location ) {
		echo $wrap_main_landmark( $search_content );
	} else {
		get_template_part( 'template-parts/search' );
	}
} else {
	$fallback_content = '';
	$has_fallback_location = false;

	if ( $is_elementor_theme_exist ) {
		ob_start();
		$has_fallback_location = elementor_theme_do_location( 'single' );
		$fallback_content = trim( ob_get_clean() );
	}

	if ( $has_fallback_location ) {
		echo $wrap_main_landmark( $fallback_content );
	} else {
		get_template_part( 'template-parts/404' );
	}
}

get_footer();
