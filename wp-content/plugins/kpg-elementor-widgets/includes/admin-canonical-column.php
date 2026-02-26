<?php
/**
 * Tymczasowa kolumna "Canonical URL" na liście postów (wp-admin/edit.php) + edycja w Quick Edit.
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
add_action( 'quick_edit_custom_box', 'kpg_quick_edit_canonical_field', 10, 2 );
add_action( 'save_post', 'kpg_save_quick_edit_canonical', 10, 2 );
add_action( 'admin_enqueue_scripts', 'kpg_canonical_column_quick_edit_script' );

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
 * Output Canonical URL for the column (with data-canonical for Quick Edit).
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function kpg_show_canonical_url_column( $column, $post_id ) {
	if ( $column !== 'kpg_canonical_url' ) {
		return;
	}
	$canonical = get_post_meta( $post_id, 'rank_math_canonical_url', true );
	echo '<span class="kpg-canonical-value" data-canonical="' . esc_attr( $canonical ) . '">';
	if ( $canonical ) {
		echo '<a href="' . esc_url( $canonical ) . '" target="_blank" rel="noopener" style="font-size:11px;word-break:break-all;">' . esc_html( $canonical ) . '</a>';
	} else {
		echo '<span style="color:#999;">—</span>';
	}
	echo '</span>';
}

/**
 * Add Canonical URL field to Quick Edit form.
 *
 * @param string $column_name Column name.
 * @param string $post_type   Post type.
 */
function kpg_quick_edit_canonical_field( $column_name, $post_type ) {
	if ( $column_name !== 'kpg_canonical_url' || $post_type !== 'post' ) {
		return;
	}
	?>
	<br class="clear" />
	<div class="inline-edit-group kpg-quick-edit-canonical">
		<label class="alignleft">
			<span class="title">Canonical URL</span>
			<span class="input-text-wrap">
				<input type="url" name="kpg_canonical_url" id="kpg_canonical_url" class="ptitle" value="" placeholder="https://..." style="width:100%;" />
			</span>
		</label>
	</div>
	<?php
}

/**
 * Save Canonical URL when Quick Edit is saved.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function kpg_save_quick_edit_canonical( $post_id, $post ) {
	// Only save when saving from Quick Edit (inline-save AJAX).
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || empty( $_POST['action'] ) || $_POST['action'] !== 'inline-save' ) {
		return;
	}
	if ( ! isset( $_POST['kpg_canonical_url'] ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( get_post_type( $post_id ) !== 'post' ) {
		return;
	}
	$canonical = sanitize_text_field( wp_unslash( $_POST['kpg_canonical_url'] ) );
	update_post_meta( $post_id, 'rank_math_canonical_url', $canonical );
}

/**
 * Enqueue script to populate Canonical URL in Quick Edit when row is opened.
 */
function kpg_canonical_column_quick_edit_script( $hook ) {
	if ( $hook !== 'edit.php' ) {
		return;
	}
	$inline = <<<'JS'
	jQuery(function($) {
		$(document).on('click', '.editinline', function() {
			var row = $(this).closest('tr');
			var canonical = row.find('.column-kpg_canonical_url .kpg-canonical-value').attr('data-canonical') || '';
			setTimeout(function() {
				$('#kpg_canonical_url').val(canonical);
			}, 50);
		});
	});
JS;
	wp_add_inline_script( 'jquery', $inline );
}
