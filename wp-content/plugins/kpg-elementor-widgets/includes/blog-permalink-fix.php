<?php
/**
 * Blog Permalink Fix
 * 
 * Adds /blog/ prefix to post permalinks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add /blog/ prefix to post permalinks
 */
function kpg_add_blog_prefix_to_post_permalink( $permalink, $post ) {
	// Safety check - ensure $post is valid
	if ( ! $post || ! is_object( $post ) || ! isset( $post->post_type ) ) {
		return $permalink;
	}
	
	// Only for posts
	if ( $post->post_type !== 'post' ) {
		return $permalink;
	}
	
	// Don't modify if already has /blog/
	if ( strpos( $permalink, '/blog/' ) !== false ) {
		return $permalink;
	}
	
	// Get home URL
	$home_url = home_url( '/' );
	
	// Remove home URL from permalink to get the path
	$path = str_replace( $home_url, '', $permalink );
	
	// Add /blog/ prefix if not already present
	if ( strpos( $path, 'blog/' ) !== 0 ) {
		$path = 'blog/' . $path;
	}
	
	return $home_url . $path;
}
// Use lower priority to avoid conflicts with Rank Math SEO
add_filter( 'post_link', 'kpg_add_blog_prefix_to_post_permalink', 20, 2 );
add_filter( 'post_type_link', 'kpg_add_blog_prefix_to_post_permalink', 20, 2 );

/**
 * Add rewrite rules for /blog/ prefix
 */
function kpg_add_blog_rewrite_rules() {
	// Only add rewrite rules if not in admin and not during AJAX
	if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}
	
	// Add rewrite rule for posts with /blog/ prefix
	add_rewrite_rule(
		'^blog/([^/]+)/?$',
		'index.php?name=$matches[1]&post_type=post',
		'top'
	);

	// Add rewrite rule for pagination
	add_rewrite_rule(
		'^blog/page/([0-9]+)/?$',
		'index.php?paged=$matches[1]&post_type=post',
		'top'
	);
}
// Use later priority to avoid conflicts with Rank Math SEO
add_action( 'init', 'kpg_add_blog_rewrite_rules', 20 );

/**
 * Fix queried_object for Rank Math SEO compatibility
 * This ensures queried_object is properly set before Rank Math tries to use it
 */
function kpg_fix_queried_object_for_rankmath() {
	// Only run on frontend
	if ( is_admin() ) {
		return;
	}
	
	// If queried_object is null and we're on a single post, try to fix it
	if ( is_single() && get_queried_object() === null ) {
		global $wp_query;
		
		// Try to get the post from query vars
		if ( isset( $wp_query->query_vars['name'] ) && ! empty( $wp_query->query_vars['name'] ) ) {
			$post = get_page_by_path( $wp_query->query_vars['name'], OBJECT, 'post' );
			if ( $post ) {
				$wp_query->queried_object = $post;
				$wp_query->queried_object_id = $post->ID;
			}
		}
	}
}
// Run early, before Rank Math SEO (priority 5)
add_action( 'wp', 'kpg_fix_queried_object_for_rankmath', 5 );

/**
 * Flush rewrite rules when needed
 * Call this after changing permalink structure
 */
function kpg_flush_rewrite_rules_if_needed() {
	if ( get_option( 'kpg_blog_permalink_flushed' ) !== '1' ) {
		kpg_add_blog_rewrite_rules();
		flush_rewrite_rules();
		update_option( 'kpg_blog_permalink_flushed', '1' );
	}
}
add_action( 'admin_init', 'kpg_flush_rewrite_rules_if_needed' );

