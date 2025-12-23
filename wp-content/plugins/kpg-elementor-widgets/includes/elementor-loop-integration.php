<?php
/**
 * Elementor Loop Grid Integration
 * 
 * This file integrates KPG Blog Sorting widget with Elementor's native loop grid.
 * When ?sort= parameter is present, it modifies the Elementor loop query.
 * 
 * @package KPG_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Modify Elementor loop grid query based on ?sort= parameter
 */
add_filter( 'elementor/query/query_args', 'kpg_modify_elementor_loop_sorting', 10, 2 );

function kpg_modify_elementor_loop_sorting( $query_args, $widget ) {
	// Check if sort parameter exists
	if ( ! isset( $_GET['sort'] ) ) {
		return $query_args;
	}
	
	$sort = sanitize_text_field( $_GET['sort'] );
	
	// Apply sorting
	if ( $sort === 'oldest' ) {
		$query_args['orderby'] = 'date';
		$query_args['order'] = 'ASC';
	} elseif ( $sort === 'newest' ) {
		$query_args['orderby'] = 'date';
		$query_args['order'] = 'DESC';
	}
	
	return $query_args;
}

/**
 * Add sort parameter to Elementor pagination links
 */
add_filter( 'paginate_links', 'kpg_add_sort_to_pagination', 10, 1 );

function kpg_add_sort_to_pagination( $link ) {
	if ( ! isset( $_GET['sort'] ) ) {
		return $link;
	}
	
	$sort = sanitize_text_field( $_GET['sort'] );
	
	// Add sort parameter to pagination links
	if ( strpos( $link, '?' ) !== false ) {
		$link .= '&sort=' . urlencode( $sort );
	} else {
		$link .= '?sort=' . urlencode( $sort );
	}
	
	return $link;
}

