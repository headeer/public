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
						<?php echo get_avatar( $author_id, 48 ); ?>
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
						<span class="kpg-post-meta-da">da</span>
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
					<div class="kpg-post-meta-share-buttons" style="display: none;">
						<button class="kpg-share-btn" data-platform="facebook" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">FB</button>
						<button class="kpg-share-btn" data-platform="twitter" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">TW</button>
						<button class="kpg-share-btn" data-platform="linkedin" 
								data-url="<?php echo esc_url( $permalink ); ?>" 
								data-title="<?php echo esc_attr( $title ); ?>">LI</button>
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

