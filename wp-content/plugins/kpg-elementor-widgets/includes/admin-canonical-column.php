<?php
/**
 * Tymczasowa kolumna "Canonical URL" na liście postów (wp-admin/edit.php).
 * Aby wyłączyć: w wp-config.php dodaj: define( 'KPG_SHOW_CANONICAL_COLUMN', false );
 *
 * @package KPG_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'KPG_SHOW_CANONICAL_COLUMN' ) && ! KPG_SHOW_CANONICAL_COLUMN ) {
	return;
}

add_filter( 'manage_posts_columns', 'kpg_add_canonical_url_column' );
add_action( 'manage_posts_custom_column', 'kpg_show_canonical_url_column', 10, 2 );

/**
 * Add Canonical URL column to posts list.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function kpg_add_canonical_url_column( $columns ) {
	$insert_after = 'title';
	$new          = [];
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( $key === $insert_after ) {
			$new['kpg_canonical_url'] = 'Canonical URL';
		}
	}
	if ( ! isset( $new['kpg_canonical_url'] ) ) {
		$new['kpg_canonical_url'] = 'Canonical URL';
	}
	return $new;
}

/**
 * Output Canonical URL for the column.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function kpg_show_canonical_url_column( $column, $post_id ) {
	if ( $column !== 'kpg_canonical_url' ) {
		return;
	}
	$canonical = get_post_meta( $post_id, 'rank_math_canonical_url', true );
	if ( $canonical ) {
		echo '<a href="' . esc_url( $canonical ) . '" target="_blank" rel="noopener" style="font-size:11px;word-break:break-all;">' . esc_html( $canonical ) . '</a>';
	} else {
		echo '<span style="color:#999;">—</span>';
	}
}
