<?php
/**
 * Author Permalink Settings
 * 
 * Allows changing author base from /author/ to /autor/ via WordPress admin settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add settings field to Permalink settings page
 */
function kpg_add_author_base_setting() {
	add_settings_field(
		'author_base',
		__( 'Author base', 'kpg-elementor-widgets' ),
		'kpg_author_base_setting_callback',
		'permalink',
		'optional'
	);
	
	register_setting( 'permalink', 'author_base' );
}
add_action( 'admin_init', 'kpg_add_author_base_setting' );

/**
 * Render author base setting field
 */
function kpg_author_base_setting_callback() {
	$author_base = get_option( 'author_base', 'autor' );
	?>
	<input name="author_base" type="text" id="author_base" value="<?php echo esc_attr( $author_base ); ?>" class="regular-text code" />
	<p class="description">
		<?php esc_html_e( 'Podstawa URL dla stron autora. Domyślnie: autor', 'kpg-elementor-widgets' ); ?>
		<br>
		<?php esc_html_e( 'Przykład: jeśli ustawisz "autor", URL będzie wyglądał jak /autor/nazwa-autora/', 'kpg-elementor-widgets' ); ?>
	</p>
	<?php
}

/**
 * Apply author base setting
 * Works with Rank Math SEO - uses higher priority to override Rank Math if needed
 */
function kpg_change_author_base() {
	global $wp_rewrite;
	$author_base = get_option( 'author_base', 'autor' );
	
	// Sanitize: only allow alphanumeric characters, hyphens, and underscores
	$author_base = sanitize_title( $author_base );
	
	// Default to 'autor' if empty
	if ( empty( $author_base ) ) {
		$author_base = 'autor';
	}
	
	$wp_rewrite->author_base = $author_base;
}
// Use priority 1 to run early, but Rank Math might override it later
// So we also hook into rank_math/author_base filter if available
add_action( 'init', 'kpg_change_author_base', 1 );

/**
 * Override Rank Math SEO author base if Rank Math is active
 */
function kpg_override_rankmath_author_base( $author_base ) {
	$custom_author_base = get_option( 'author_base', 'autor' );
	if ( ! empty( $custom_author_base ) ) {
		return sanitize_title( $custom_author_base );
	}
	return $author_base;
}
// Hook into Rank Math SEO author base filter if it exists
add_filter( 'rank_math/author_base', 'kpg_override_rankmath_author_base', 10, 1 );

/**
 * Also hook into WordPress author_rewrite_base filter (used by some SEO plugins)
 */
function kpg_override_author_rewrite_base( $author_base ) {
	$custom_author_base = get_option( 'author_base', 'autor' );
	if ( ! empty( $custom_author_base ) ) {
		return sanitize_title( $custom_author_base );
	}
	return $author_base;
}
add_filter( 'author_rewrite_base', 'kpg_override_author_rewrite_base', 10, 1 );

/**
 * Flush rewrite rules when author base changes
 */
function kpg_flush_rewrite_rules_on_author_base_change() {
	$current_author_base = get_option( 'author_base', 'autor' );
	$flushed_author_base = get_option( 'kpg_author_base_flushed', '' );
	
	if ( $current_author_base !== $flushed_author_base ) {
		flush_rewrite_rules( false );
		update_option( 'kpg_author_base_flushed', $current_author_base );
	}
}
add_action( 'admin_init', 'kpg_flush_rewrite_rules_on_author_base_change' );

