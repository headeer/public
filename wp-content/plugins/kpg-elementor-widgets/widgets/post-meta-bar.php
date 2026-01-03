<?php
/**
 * KPG Post Meta Bar Widget
 * 
 * Prompt #49: "wyswietl imie i nazwisko" (first_name + last_name)
 * Autor + Data + Share buttons
 * Używane na górze pojedynczego posta
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Post_Meta_Bar_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-post-meta-bar';
	}

	public function get_title() {
		return esc_html__( 'KPG Post Meta Bar', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-post-info';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_style_depends() {
		return [ 'kpg-post-meta-bar-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-post-meta-bar-script' ];
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
			'show_avatar',
			[
				'label' => esc_html__( 'Show Avatar', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label' => esc_html__( 'Show Date', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_share',
			[
				'label' => esc_html__( 'Show Share Buttons', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		// Get author data
		$author_id = get_the_author_meta( 'ID' );
		$first = get_the_author_meta( 'first_name', $author_id );
		$last = get_the_author_meta( 'last_name', $author_id );
		$name = trim( $first . ' ' . $last );
		if ( empty( $name ) ) {
			$name = get_the_author();
		}
		
		$date = get_the_date( 'd F Y' ); // "07 lipiec 2025"
		$permalink = get_permalink();
		$title = get_the_title();
		?>
		<div class="kpg-post-meta-bar">
			<!-- Left: Avatar + Author -->
			<div class="kpg-post-meta-author-section">
				<?php if ( $settings['show_avatar'] === 'yes' ) : ?>
					<div class="kpg-post-meta-avatar">
						<?php 
						$avatar_url = kpg_get_author_avatar_url( $author_id, 48 );
						?>
						<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="48" height="48" style="border-radius: 50%;" />
					</div>
				<?php endif; ?>
				
				<div class="kpg-post-meta-author-info">
					<span class="kpg-post-meta-author-label">autor</span>
					<span class="kpg-post-meta-author-name"><?php echo esc_html( $name ); ?></span>
				</div>
			</div>
			
			<!-- Center: Date -->
			<?php if ( $settings['show_date'] === 'yes' ) : ?>
				<div class="kpg-post-meta-date-section">
					<div class="kpg-post-meta-date-label">
						<span class="kpg-post-meta-date-text">Artykuł zgodny ze stanem prawnym na dzień:</span>
					</div>
					<span class="kpg-post-meta-date-value"><?php echo esc_html( $date ); ?></span>
				</div>
			<?php endif; ?>
			
			<!-- Right: Share -->
			<?php if ( $settings['show_share'] === 'yes' ) : ?>
				<div class="kpg-post-meta-share-section">
					<div class="kpg-post-meta-share-icon">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
							<path d="M13.5 10.5c-.83 0-1.58.33-2.13.87L6.7 8.71c.08-.23.13-.48.13-.71s-.05-.48-.13-.71l4.67-2.66c.55.54 1.3.87 2.13.87 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .23.05.48.13.71L5.96 5.87C5.41 5.33 4.66 5 3.83 5c-1.66 0-3 1.34-3 3s1.34 3 3 3c.83 0 1.58-.33 2.13-.87l4.67 2.66c-.08.23-.13.48-.13.71 0 1.66 1.34 3 3 3s3-1.34 3-3-1.34-3-3-3z" fill="#404848"/>
						</svg>
					</div>
					<span class="kpg-post-meta-share-text">udostępnij</span>
					
					<!-- Hidden share buttons (shown on click) -->
					<div class="kpg-post-meta-share-buttons">
						<button class="kpg-share-btn" data-platform="facebook" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">
							<svg class="kpg-share-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
								<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
							</svg>
							<span class="kpg-share-btn-text">Facebook</span>
						</button>
						<button class="kpg-share-btn" data-platform="twitter" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">
							<svg class="kpg-share-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
								<path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
							</svg>
							<span class="kpg-share-btn-text">Twitter</span>
						</button>
						<button class="kpg-share-btn" data-platform="linkedin" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">
							<svg class="kpg-share-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
								<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
							</svg>
							<span class="kpg-share-btn-text">LinkedIn</span>
						</button>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kpg-post-meta-bar">
			<div class="kpg-post-meta-author-section">
				<# if (settings.show_avatar === 'yes') { #>
					<div class="kpg-post-meta-avatar" style="background: #ccc; width: 48px; height: 48px; border-radius: 50%;"></div>
				<# } #>
				<div class="kpg-post-meta-info">
					<span class="kpg-post-meta-author">Author Name</span>
					<# if (settings.show_date === 'yes') { #>
						<span class="kpg-post-meta-date">23.12.2025</span>
					<# } #>
				</div>
			</div>
			<# if (settings.show_share === 'yes') { #>
				<div class="kpg-post-meta-share">
					<button class="kpg-share-btn">
						<svg width="20" height="20"><circle cx="10" cy="10" r="8" fill="#404848"/></svg>
					</button>
					<button class="kpg-share-btn">
						<svg width="20" height="20"><circle cx="10" cy="10" r="8" fill="#404848"/></svg>
					</button>
					<button class="kpg-share-btn">
						<svg width="20" height="20"><circle cx="10" cy="10" r="8" fill="#404848"/></svg>
					</button>
				</div>
			<# } #>
		</div>
		<?php
	}
}

