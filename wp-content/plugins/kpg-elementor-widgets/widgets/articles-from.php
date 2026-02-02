<?php
/**
 * KPG Articles From Widget
 * 
 * Prompt #59 (line 13900): "widget zrob musimy do artykuly od dac"
 * Design: width 385px, padding 16px, gap 32px, bg #e3ebec
 * Zawiera: title, image, name, position, bio, social links
 * Dynamiczne z profilu autora WordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Articles_From_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-articles-from';
	}

	public function get_title() {
		return esc_html__( 'KPG Articles From', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_style_depends() {
		return [ 'kpg-articles-from-style' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'source',
			[
				'label' => esc_html__( 'Author Source', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'archive',
				'options' => [
					'archive' => esc_html__( 'Author Archive Page (Auto-detect)', 'kpg-elementor-widgets' ),
					'custom' => esc_html__( 'Custom Author', 'kpg-elementor-widgets' ),
				],
				'description' => esc_html__( 'Widget will only display on author archive pages (/autor/author-name/). Use "Auto-detect" to automatically show the author from the current page.', 'kpg-elementor-widgets' ),
			]
		);

		$authors = get_users( [ 'who' => 'authors' ] );
		$author_options = [];
		foreach ( $authors as $author ) {
			$author_options[ $author->ID ] = $author->display_name;
		}

		$this->add_control(
			'custom_author_id',
			[
				'label' => esc_html__( 'Select Author', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'options' => $author_options,
				'condition' => [
					'source' => 'custom',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function get_author_data( $author_id = null ) {
		if ( ! $author_id ) {
			// Try to get from author archive page
			if ( is_author() ) {
				$queried_object = get_queried_object();
				if ( $queried_object && isset( $queried_object->ID ) ) {
					$author_id = $queried_object->ID;
				} else {
					$author_id = get_queried_object_id();
				}
			}
			
			// Fallback to current post author
			if ( ! $author_id ) {
				$author_id = get_the_author_meta( 'ID' );
			}
		}

		// Full name - ALWAYS use first_name + last_name (not display_name or user_nicename)
		$first = get_the_author_meta( 'first_name', $author_id );
		$last = get_the_author_meta( 'last_name', $author_id );
		$name = trim( $first . ' ' . $last );
		
		// Only fallback to display_name if both first and last are empty
		if ( empty( $name ) ) {
			$name = get_the_author_meta( 'display_name', $author_id );
		}

		// Position/Title
		$position = get_user_meta( $author_id, 'author_title', true );
		if ( empty( $position ) ) {
			$position = get_user_meta( $author_id, 'position', true );
		}
		if ( empty( $position ) ) {
			$position = 'RADCA PRAWNY';
		}

		// Bio
		$bio = get_the_author_meta( 'description', $author_id );
		if ( empty( $bio ) ) {
			$bio = 'Radca Prawny, absolwent Krakowskiej Akademii.';
		}

		// Avatar
		$avatar = kpg_get_author_avatar_url( $author_id, 300 );

		// Social (check both old and new field names for backward compatibility)
		$linkedin = get_user_meta( $author_id, 'author_linkedin', true );
		if ( empty( $linkedin ) ) {
			$linkedin = get_user_meta( $author_id, 'linkedin_url', true );
		}
		$facebook = get_user_meta( $author_id, 'author_facebook', true );
		if ( empty( $facebook ) ) {
			$facebook = get_user_meta( $author_id, 'facebook_url', true );
		}

		return [
			'name' => $name,
			'position' => $position,
			'bio' => $bio,
			'avatar' => $avatar,
			'linkedin' => $linkedin,
			'facebook' => $facebook,
		];
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Only show on author archive pages
		if ( ! is_author() ) {
			return;
		}
		
		// Get author ID from current author archive page
		$author_id = null;
		
		// First, try to get from queried object (most reliable)
		$queried_object = get_queried_object();
		if ( $queried_object && isset( $queried_object->ID ) ) {
			$author_id = $queried_object->ID;
		} else {
			$author_id = get_queried_object_id();
		}
		
		// Fallback: check query vars
		if ( ! $author_id ) {
			$author_var = get_query_var( 'author' );
			if ( $author_var ) {
				$author_id = intval( $author_var );
			}
		}
		
		// Fallback: check author_name query var
		if ( ! $author_id ) {
			$author_name = get_query_var( 'author_name' );
			if ( $author_name ) {
				$author = get_user_by( 'slug', $author_name );
				if ( $author ) {
					$author_id = $author->ID;
				}
			}
		}
		
		// Fallback: check URL directly
		if ( ! $author_id ) {
			$request_uri = $_SERVER['REQUEST_URI'] ?? '';
			if ( preg_match( '#/(?:autor|author)/([^/]+)/?#', $request_uri, $matches ) ) {
				$author_slug = $matches[1];
				$author = get_user_by( 'slug', $author_slug );
				if ( $author ) {
					$author_id = $author->ID;
				}
			}
		}
		
		// If still no author ID, don't render
		if ( ! $author_id ) {
			return;
		}
		
		// Allow override via settings (for custom author selection)
		// Note: Even with custom, we still require is_author() to be true
		if ( $settings['source'] === 'custom' && ! empty( $settings['custom_author_id'] ) ) {
			$custom_author_id = intval( $settings['custom_author_id'] );
			// Verify the custom author exists
			if ( get_user_by( 'ID', $custom_author_id ) ) {
				$author_id = $custom_author_id;
			}
		}

		$author = $this->get_author_data( $author_id );
		
		// Get author URL with custom base
		$author_base = get_option( 'author_base', 'autor' );
		$author_slug = get_the_author_meta( 'user_nicename', $author_id );
		$author_url = home_url( '/' . $author_base . '/' . $author_slug . '/' );
		?>
		<aside class="kpg-author-sidebar" aria-label="<?php echo esc_attr( sprintf( __( 'Artykuły od %s', 'kpg-elementor-widgets' ), $author['name'] ) ); ?>">
			<article class="kpg-author-card" itemscope itemtype="https://schema.org/Person">
				
				<header class="kpg-author-card-header">
					<h2 class="kpg-author-card-title"><?php echo esc_html__( 'Artykuły od:', 'kpg-elementor-widgets' ); ?></h2>
				</header>
				
				<figure class="kpg-author-card-image">
					<a href="<?php echo esc_url( $author_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Zobacz wszystkie artykuły od %s', 'kpg-elementor-widgets' ), $author['name'] ) ); ?>">
						<img 
							src="<?php echo esc_url( $author['avatar'] ); ?>" 
							alt="<?php echo esc_attr( sprintf( __( 'Zdjęcie autora %s', 'kpg-elementor-widgets' ), $author['name'] ) ); ?>"
							width="353"
							height="269"
							loading="lazy"
							itemprop="image"
						>
					</a>
				</figure>
				
				<div class="kpg-author-card-body">
					<div class="kpg-author-card-meta">
						<h3 class="kpg-author-card-name" itemprop="name">
							<a href="<?php echo esc_url( $author_url ); ?>" rel="author">
								<?php echo esc_html( $author['name'] ); ?>
							</a>
						</h3>
						<p class="kpg-author-card-position" itemprop="jobTitle">
							<?php echo esc_html( $author['position'] ); ?>
						</p>
					</div>
					
					<div class="kpg-author-card-bio" itemprop="description">
						<?php echo wp_kses_post( wpautop( $author['bio'] ) ); ?>
					</div>
					
					<nav class="kpg-author-card-social" aria-label="<?php echo esc_attr__( 'Linki społecznościowe autora', 'kpg-elementor-widgets' ); ?>">
						<?php if ( ! empty( $author['linkedin'] ) ) : ?>
							<a 
								href="<?php echo esc_url( $author['linkedin'] ); ?>" 
								target="_blank" 
								rel="noopener noreferrer me" 
								class="kpg-author-card-social-link"
								aria-label="<?php echo esc_attr( sprintf( __( 'Profil LinkedIn autora %s', 'kpg-elementor-widgets' ), $author['name'] ) ); ?>"
								itemprop="sameAs"
							>
								<?php echo esc_html__( 'LINKEDIN', 'kpg-elementor-widgets' ); ?>
							</a>
						<?php else : ?>
							<span class="kpg-author-card-social-link kpg-author-card-social-link--disabled" aria-label="<?php echo esc_attr__( 'LinkedIn nie jest ustawiony', 'kpg-elementor-widgets' ); ?>">
								<?php echo esc_html__( 'LINKEDIN', 'kpg-elementor-widgets' ); ?>
							</span>
						<?php endif; ?>
						
						<?php if ( ! empty( $author['facebook'] ) ) : ?>
							<a 
								href="<?php echo esc_url( $author['facebook'] ); ?>" 
								target="_blank" 
								rel="noopener noreferrer me" 
								class="kpg-author-card-social-link"
								aria-label="<?php echo esc_attr( sprintf( __( 'Profil Facebook autora %s', 'kpg-elementor-widgets' ), $author['name'] ) ); ?>"
								itemprop="sameAs"
							>
								<?php echo esc_html__( 'FACEBOOK', 'kpg-elementor-widgets' ); ?>
							</a>
						<?php else : ?>
							<span class="kpg-author-card-social-link kpg-author-card-social-link--disabled" aria-label="<?php echo esc_attr__( 'Facebook nie jest ustawiony', 'kpg-elementor-widgets' ); ?>">
								<?php echo esc_html__( 'FACEBOOK', 'kpg-elementor-widgets' ); ?>
							</span>
						<?php endif; ?>
					</nav>
				</div>
			</article>
		</aside>
		<?php
	}

	protected function content_template() {
		?>
		<aside class="kpg-author-sidebar">
			<article class="kpg-author-card">
				<header class="kpg-author-card-header">
					<h2 class="kpg-author-card-title"><?php echo esc_html__( 'Artykuły od:', 'kpg-elementor-widgets' ); ?></h2>
				</header>
				<figure class="kpg-author-card-image" style="background: #e3ebec; height: 269px; border-radius: 8px;"></figure>
				<div class="kpg-author-card-body">
					<div class="kpg-author-card-meta">
						<h3 class="kpg-author-card-name">Mateusz Pęczkowski</h3>
						<p class="kpg-author-card-position">RADCA PRAWNY</p>
					</div>
					<div class="kpg-author-card-bio">Bio text...</div>
					<nav class="kpg-author-card-social">
						<span class="kpg-author-card-social-link kpg-author-card-social-link--disabled"><?php echo esc_html__( 'LINKEDIN', 'kpg-elementor-widgets' ); ?></span>
						<span class="kpg-author-card-social-link kpg-author-card-social-link--disabled"><?php echo esc_html__( 'FACEBOOK', 'kpg-elementor-widgets' ); ?></span>
					</nav>
				</div>
			</article>
		</aside>
		<?php
	}
}


