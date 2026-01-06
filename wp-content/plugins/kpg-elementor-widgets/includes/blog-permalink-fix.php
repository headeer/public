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
 * Change author base from /author/ to /autor/
 * NOTE: This is now handled by author-permalink-settings.php
 * Keeping this for backward compatibility, but it will be overridden by settings
 */
function kpg_change_author_base_legacy() {
	global $wp_rewrite;
	// Only apply if no setting exists (backward compatibility)
	if ( ! get_option( 'author_base' ) ) {
		$wp_rewrite->author_base = 'autor';
	}
}
// Lower priority so settings can override
add_action( 'init', 'kpg_change_author_base_legacy', 2 );

/**
 * Add rewrite rules for /blog/ prefix
 */
function kpg_add_blog_rewrite_rules() {
	// Add rewrite rule for posts with /blog/ prefix
	add_rewrite_rule(
		'^blog/([^/]+)/?$',
		'index.php?name=$matches[1]&post_type=post',
		'top'
	);

	// Add rewrite rule for pagination
	// Check if posts page is set
	$posts_page_id = get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		// Use page_id - this works regardless of page slug
		add_rewrite_rule(
			'^blog/page/([0-9]+)/?$',
			'index.php?page_id=' . $posts_page_id . '&paged=$matches[1]',
			'top'
		);
	} else {
		// Fallback to post_type
		add_rewrite_rule(
			'^blog/page/([0-9]+)/?$',
			'index.php?paged=$matches[1]&post_type=post',
			'top'
		);
	}
}
// Use later priority to avoid conflicts with Rank Math SEO
add_action( 'init', 'kpg_add_blog_rewrite_rules', 20 );

/**
 * Parse request for /blog/page/X/ URLs
 * This ensures WordPress properly recognizes pagination on blog archive
 */
function kpg_parse_blog_pagination_request( $query_vars ) {
	// Only on frontend
	if ( is_admin() ) {
		return $query_vars;
	}
	
	// Check if we're on /blog/page/X/
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	if ( preg_match( '#^/blog/page/(\d+)/?#', $request_uri, $matches ) ) {
		$page_num = intval( $matches[1] );
		
		// Get posts page ID
		$posts_page_id = get_option( 'page_for_posts' );
		if ( $posts_page_id ) {
			// Set query vars for posts page pagination using page_id
			$query_vars['page_id'] = $posts_page_id;
			$query_vars['paged'] = $page_num;
			$query_vars['page'] = '';
			// Clear pagename to avoid conflicts
			unset( $query_vars['pagename'] );
		} else {
			// Fallback: set as blog archive pagination
			$query_vars['paged'] = $page_num;
			$query_vars['post_type'] = 'post';
		}
	}
	
	return $query_vars;
}
add_filter( 'request', 'kpg_parse_blog_pagination_request', 10, 1 );

/**
 * Fix query for blog pagination
 * Ensures the main query is set up correctly for paginated blog archive
 */
function kpg_fix_blog_pagination_query( $query ) {
	// Only on frontend and main query
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	
	// Check if we're on /blog/page/X/
	$request_uri = $_SERVER['REQUEST_URI'] ?? '';
	if ( preg_match( '#^/blog/page/(\d+)/?#', $request_uri, $matches ) ) {
		$page_num = intval( $matches[1] );
		
		// Get posts page ID
		$posts_page_id = get_option( 'page_for_posts' );
		if ( $posts_page_id ) {
			// Set query to show posts from blog archive
			$query->set( 'post_type', 'post' );
			$query->set( 'paged', $page_num );
			$query->set( 'posts_per_page', get_option( 'posts_per_page' ) );
			
			// Make sure it's treated as blog archive (not page)
			$query->is_home = false;
			$query->is_page = false;
			$query->is_paged = true;
			$query->is_archive = false;
		}
	}
}
add_action( 'pre_get_posts', 'kpg_fix_blog_pagination_query', 5, 1 );


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
 * 
 * Note: Reset this by deleting the option 'kpg_blog_permalink_flushed' 
 * or changing its value to force a flush
 */
function kpg_flush_rewrite_rules_if_needed() {
	// Check version to force flush when rewrite rules change
	$current_version = '2.3'; // Increment this when rewrite rules change (2.3 = added author base change)
	$flushed_version = get_option( 'kpg_blog_permalink_flushed_version' );
	
	if ( $flushed_version !== $current_version ) {
		// Change author base
		kpg_change_author_base();
		// Add blog rewrite rules
		kpg_add_blog_rewrite_rules();
		// Flush rewrite rules
		flush_rewrite_rules( false ); // false = don't write to .htaccess, just update rules
		update_option( 'kpg_blog_permalink_flushed_version', $current_version );
		update_option( 'kpg_blog_permalink_flushed', '1' );
	}
}
add_action( 'admin_init', 'kpg_flush_rewrite_rules_if_needed' );

/**
 * Get pagination URL for blog archive
 * Returns /blog/page/X/ format instead of ?paged=X
 */
function kpg_get_blog_pagination_url( $page ) {
	$page = intval( $page );
	
	// Get blog page URL (posts page)
	$posts_page_id = get_option( 'page_for_posts' );
	if ( $posts_page_id ) {
		$base_url = get_permalink( $posts_page_id );
	} else {
		// Fallback to /blog/
		$base_url = home_url( '/blog/' );
	}
	
	// Remove trailing slash from base URL
	$base_url = rtrim( $base_url, '/' );
	
	// Page 1 = base URL, page 2+ = /page/X/
	if ( $page <= 1 ) {
		return $base_url;
	} else {
		return $base_url . '/page/' . $page . '/';
	}
}

