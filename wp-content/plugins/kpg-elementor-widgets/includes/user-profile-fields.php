<?php
/**
 * KPG User Profile Fields
 * 
 * Adds custom fields to WordPress user profile:
 * - Author Avatar Image (custom upload)
 * - Author Title (e.g., "RADCA PRAWNY")
 * - LinkedIn URL
 * - Facebook URL
 * - Bio/Description (uses WordPress default description field)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get author avatar URL - checks custom avatar first, then falls back to Gravatar
 * 
 * @param int $user_id User ID
 * @param int $size Avatar size (default: 96)
 * @return string Avatar URL
 */
function kpg_get_author_avatar_url( $user_id, $size = 96 ) {
	// First, check for custom avatar image
	$avatar_image_id = get_user_meta( $user_id, 'author_avatar_image', true );
	if ( $avatar_image_id ) {
		$avatar_url = wp_get_attachment_image_url( $avatar_image_id, [ $size, $size ] );
		if ( $avatar_url ) {
			return $avatar_url;
		}
	}
	
	// Fallback to Gravatar
	return get_avatar_url( $user_id, [ 'size' => $size ] );
}

/**
 * Add custom fields to user profile
 */
function kpg_add_user_profile_fields( $user ) {
	?>
	<h3><?php esc_html_e( 'Informacje o autorze', 'kpg-elementor-widgets' ); ?></h3>
	<table class="form-table">
		<tr>
			<th>
				<label for="author_avatar_image"><?php esc_html_e( 'Zdjęcie profilowe', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<?php
				$avatar_image_id = get_user_meta( $user->ID, 'author_avatar_image', true );
				$avatar_image_url = '';
				if ( $avatar_image_id ) {
					$avatar_image_url = wp_get_attachment_image_url( $avatar_image_id, 'thumbnail' );
				}
				?>
				<div class="kpg-author-avatar-upload">
					<div class="kpg-author-avatar-preview" style="margin-bottom: 10px;">
						<?php if ( $avatar_image_url ) : ?>
							<img src="<?php echo esc_url( $avatar_image_url ); ?>" alt="Avatar preview" style="max-width: 150px; height: auto; border-radius: 8px; display: block;" />
						<?php else : ?>
							<div style="width: 150px; height: 150px; background: #e3ebec; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #899596; font-size: 14px;">
								Brak zdjęcia
							</div>
						<?php endif; ?>
					</div>
					<input type="hidden" name="author_avatar_image" id="author_avatar_image" value="<?php echo esc_attr( $avatar_image_id ); ?>" />
					<button type="button" class="button kpg-upload-avatar-button" data-user-id="<?php echo esc_attr( $user->ID ); ?>">
						<?php echo $avatar_image_id ? esc_html__( 'Zmień zdjęcie', 'kpg-elementor-widgets' ) : esc_html__( 'Wybierz zdjęcie', 'kpg-elementor-widgets' ); ?>
					</button>
					<?php if ( $avatar_image_id ) : ?>
						<button type="button" class="button kpg-remove-avatar-button" style="margin-left: 10px;">
							<?php esc_html_e( 'Usuń zdjęcie', 'kpg-elementor-widgets' ); ?>
						</button>
					<?php endif; ?>
					<p class="description">
						<?php esc_html_e( 'Wybierz zdjęcie profilowe autora. Jeśli nie wybierzesz zdjęcia, zostanie użyty Gravatar lub domyślny avatar.', 'kpg-elementor-widgets' ); ?>
					</p>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<label for="author_title"><?php esc_html_e( 'Tytuł/Stanowisko', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<input type="text" 
					   name="author_title" 
					   id="author_title" 
					   value="<?php echo esc_attr( get_user_meta( $user->ID, 'author_title', true ) ); ?>" 
					   class="regular-text" 
					   placeholder="<?php esc_attr_e( 'np. RADCA PRAWNY', 'kpg-elementor-widgets' ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Tytuł wyświetlany przy imieniu i nazwisku autora (np. RADCA PRAWNY, ADWOKAT)', 'kpg-elementor-widgets' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="author_linkedin"><?php esc_html_e( 'LinkedIn URL', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<input type="url" 
					   name="author_linkedin" 
					   id="author_linkedin" 
					   value="<?php echo esc_url( get_user_meta( $user->ID, 'author_linkedin', true ) ); ?>" 
					   class="regular-text" 
					   placeholder="https://linkedin.com/in/username" />
				<p class="description">
					<?php esc_html_e( 'Pełny URL do profilu LinkedIn', 'kpg-elementor-widgets' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="author_facebook"><?php esc_html_e( 'Facebook URL', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<input type="url" 
					   name="author_facebook" 
					   id="author_facebook" 
					   value="<?php echo esc_url( get_user_meta( $user->ID, 'author_facebook', true ) ); ?>" 
					   class="regular-text" 
					   placeholder="https://facebook.com/username" />
				<p class="description">
					<?php esc_html_e( 'Pełny URL do profilu Facebook', 'kpg-elementor-widgets' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<label for="description"><?php esc_html_e( 'Biografia', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<textarea name="description" 
						  id="description" 
						  rows="5" 
						  cols="30" 
						  class="regular-text"><?php echo esc_textarea( get_the_author_meta( 'description', $user->ID ) ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Krótki opis autora wyświetlany w sekcji o autorze (np. "Radca Prawny, absolwent Krakowskiej Akademii im. Andrzeja Frycza Modrzewskiego. Aplikację radcowską ukończył przy Okręgowej Izbie Radców Prawnych w Warszawie.")', 'kpg-elementor-widgets' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php esc_html_e( 'URL autora', 'kpg-elementor-widgets' ); ?></label>
			</th>
			<td>
				<?php
				$current_slug = $user->user_nicename;
				$author_base = get_option( 'author_base', 'autor' );
				$current_url = home_url( '/' . $author_base . '/' . $current_slug . '/' );
				$generated_slug = kpg_generate_author_slug( $user->ID );
				$generated_url = home_url( '/' . $author_base . '/' . $generated_slug . '/' );
				?>
				<p>
					<strong><?php esc_html_e( 'Aktualny URL:', 'kpg-elementor-widgets' ); ?></strong><br>
					<code><?php echo esc_url( $current_url ); ?></code>
				</p>
				<?php if ( $current_slug !== $generated_slug && ! empty( $generated_slug ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'Sugerowany URL (na podstawie imienia i nazwiska):', 'kpg-elementor-widgets' ); ?></strong><br>
						<code><?php echo esc_url( $generated_url ); ?></code>
					</p>
					<p class="description">
						<?php esc_html_e( 'URL autora jest automatycznie generowany na podstawie imienia i nazwiska (bez polskich znaków) przy zapisie profilu. Jeśli chcesz zaktualizować URL, zapisz profil po zmianie imienia lub nazwiska.', 'kpg-elementor-widgets' ); ?>
					</p>
				<?php else : ?>
					<p class="description">
						<?php esc_html_e( 'URL autora jest automatycznie generowany na podstawie imienia i nazwiska (bez polskich znaków). URL zostanie zaktualizowany automatycznie przy zapisie profilu.', 'kpg-elementor-widgets' ); ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'kpg_add_user_profile_fields' );
add_action( 'edit_user_profile', 'kpg_add_user_profile_fields' );

/**
 * Convert Polish characters to ASCII equivalents
 * 
 * @param string $text Text with Polish characters
 * @return string Text without Polish characters
 */
function kpg_remove_polish_characters( $text ) {
	$polish_chars = [
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n',
		'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z',
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'E', 'Ł' => 'L', 'Ń' => 'N',
		'Ó' => 'O', 'Ś' => 'S', 'Ź' => 'Z', 'Ż' => 'Z',
	];
	
	return strtr( $text, $polish_chars );
}

/**
 * Generate author slug from first name and last name
 * 
 * @param int $user_id User ID
 * @return string Author slug (e.g., "mateusz-peczkowski")
 */
function kpg_generate_author_slug( $user_id ) {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}
	
	// Get first_name and last_name from user object (stored in users table, not meta)
	$first_name = $user->first_name;
	$last_name = $user->last_name;
	
	// Combine first and last name
	$full_name = trim( $first_name . ' ' . $last_name );
	
	// If still empty, fallback to display_name
	if ( empty( $full_name ) ) {
		$full_name = $user->display_name;
	}
	
	// If still empty, fallback to user_nicename (don't change it)
	if ( empty( $full_name ) ) {
		return $user->user_nicename;
	}
	
	// Remove Polish characters
	$full_name = kpg_remove_polish_characters( $full_name );
	
	// Convert to slug
	$slug = sanitize_title( $full_name );
	
	// If slug is still empty, use current user_nicename as fallback
	if ( empty( $slug ) ) {
		$slug = $user->user_nicename;
	}
	
	return $slug;
}

/**
 * Save custom user profile fields
 */
function kpg_save_user_profile_fields( $user_id ) {
	// Check if current user can edit this user
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	// Auto-update user_nicename based on first_name + last_name (without Polish characters)
	// Check if first_name or last_name was submitted in the form
	if ( isset( $_POST['first_name'] ) || isset( $_POST['last_name'] ) ) {
		$new_slug = kpg_generate_author_slug( $user_id );
		
		// Only update if slug changed and is not empty
		$current_user = get_userdata( $user_id );
		if ( $current_user && $new_slug && $new_slug !== $current_user->user_nicename ) {
			// Check if slug is already taken by another user
			$existing_user = get_user_by( 'slug', $new_slug );
			if ( ! $existing_user || $existing_user->ID === $user_id ) {
				wp_update_user( [
					'ID' => $user_id,
					'user_nicename' => $new_slug,
				] );
				
				// Flush rewrite rules to ensure new URL works
				flush_rewrite_rules( false );
			}
		}
	}

	// Save author avatar image
	if ( isset( $_POST['author_avatar_image'] ) ) {
		$avatar_image_id = intval( $_POST['author_avatar_image'] );
		if ( $avatar_image_id > 0 ) {
			// Verify that the attachment exists and belongs to the current user
			$attachment = get_post( $avatar_image_id );
			if ( $attachment && $attachment->post_type === 'attachment' ) {
				update_user_meta( $user_id, 'author_avatar_image', $avatar_image_id );
			}
		} else {
			// Remove avatar if empty
			delete_user_meta( $user_id, 'author_avatar_image' );
		}
	}

	// Save author title
	if ( isset( $_POST['author_title'] ) ) {
		update_user_meta( $user_id, 'author_title', sanitize_text_field( $_POST['author_title'] ) );
	}

	// Save LinkedIn URL
	if ( isset( $_POST['author_linkedin'] ) ) {
		$linkedin_url = esc_url_raw( $_POST['author_linkedin'] );
		update_user_meta( $user_id, 'author_linkedin', $linkedin_url );
		// Also save as linkedin_url for backward compatibility
		update_user_meta( $user_id, 'linkedin_url', $linkedin_url );
	}

	// Save Facebook URL
	if ( isset( $_POST['author_facebook'] ) ) {
		$facebook_url = esc_url_raw( $_POST['author_facebook'] );
		update_user_meta( $user_id, 'author_facebook', $facebook_url );
		// Also save as facebook_url for backward compatibility
		update_user_meta( $user_id, 'facebook_url', $facebook_url );
	}

	// Save description/biography
	if ( isset( $_POST['description'] ) ) {
		$description = sanitize_textarea_field( $_POST['description'] );
		// Update via wp_update_user to ensure it's saved properly
		wp_update_user( [
			'ID' => $user_id,
			'description' => $description,
		] );
	}
}
add_action( 'personal_options_update', 'kpg_save_user_profile_fields' );
add_action( 'edit_user_profile_update', 'kpg_save_user_profile_fields' );

