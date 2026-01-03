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
				'default' => 'current',
				'options' => [
					'current' => esc_html__( 'Current Post Author', 'kpg-elementor-widgets' ),
					'custom' => esc_html__( 'Custom Author', 'kpg-elementor-widgets' ),
				],
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
			$author_id = get_the_author_meta( 'ID' );
		}

		// Full name
		$first = get_the_author_meta( 'first_name', $author_id );
		$last = get_the_author_meta( 'last_name', $author_id );
		$name = trim( $first . ' ' . $last );
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
		
		$author_id = null;
		if ( $settings['source'] === 'custom' && ! empty( $settings['custom_author_id'] ) ) {
			$author_id = intval( $settings['custom_author_id'] );
		}

		$author = $this->get_author_data( $author_id );
		?>
		<div class="kpg-articles-from-container">
			<div class="kpg-articles-from-content">
				<div class="kpg-articles-from-header">
					<span class="kpg-articles-from-title">ARTYKUŁY OD:</span>
				</div>
				
				<div class="kpg-articles-from-image">
					<img src="<?php echo esc_url( $author['avatar'] ); ?>" alt="<?php echo esc_attr( $author['name'] ); ?>">
				</div>
				
				<div class="kpg-articles-from-info">
					<div class="kpg-articles-from-name-section">
						<span class="kpg-articles-from-name"><?php echo esc_html( $author['name'] ); ?></span>
						<span class="kpg-articles-from-position"><?php echo esc_html( $author['position'] ); ?></span>
					</div>
					
					<div class="kpg-articles-from-bio">
						<?php echo wp_kses_post( wpautop( $author['bio'] ) ); ?>
					</div>
					
					<?php if ( $author['linkedin'] || $author['facebook'] ) : ?>
						<div class="kpg-articles-from-social">
							<?php if ( $author['linkedin'] ) : ?>
								<a href="<?php echo esc_url( $author['linkedin'] ); ?>" target="_blank" rel="noopener" class="kpg-articles-from-social-link">
									<span>LINKEDIN</span>
								</a>
							<?php endif; ?>
							<?php if ( $author['facebook'] ) : ?>
								<a href="<?php echo esc_url( $author['facebook'] ); ?>" target="_blank" rel="noopener" class="kpg-articles-from-social-link">
									<span>FACEBOOK</span>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kpg-articles-from-container">
			<div class="kpg-articles-from-content">
				<div class="kpg-articles-from-header">
					<span class="kpg-articles-from-title">ARTYKUŁY OD:</span>
				</div>
				<div class="kpg-articles-from-image" style="background: #e3ebec; height: 269px;"></div>
				<div class="kpg-articles-from-info">
					<div class="kpg-articles-from-name-section">
						<span class="kpg-articles-from-name">Mateusz Pęczkowski</span>
						<span class="kpg-articles-from-position">RADCA PRAWNY</span>
					</div>
					<div class="kpg-articles-from-bio">Bio text...</div>
					<div class="kpg-articles-from-social">
						<a href="#" class="kpg-articles-from-social-link"><span>LINKEDIN</span></a>
						<a href="#" class="kpg-articles-from-social-link"><span>FACEBOOK</span></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}


