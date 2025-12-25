<?php
/**
 * KPG Team Slider Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Wyświetla slider z członkami zespołu z obrazkami, tekstami i nawigacją
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class KPG_Elementor_Team_Slider_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-team-slider';
	}

	public function get_title() {
		return esc_html__( 'KPG Team Slider', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'team', 'slider', 'swiper', 'members', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-team-slider-style', 'swiper' ];
	}

	public function get_script_depends() {
		return [ 'kpg-team-slider-script', 'swiper' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Team Members', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'TEAM',
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Thumbnail Image', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Image for thumbnail navigation', 'kpg-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'main_image',
			[
				'label' => esc_html__( 'Main Image', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Large image displayed in the main slider', 'kpg-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'object_position',
			[
				'label' => esc_html__( 'Main Image Object Position (Desktop)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'center center',
				'description' => esc_html__( 'e.g., "center calc(50% + 50px)"', 'kpg-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => esc_html__( 'Name', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Imię Nazwisko',
			]
		);

		$repeater->add_control(
			'job_title',
			[
				'label' => esc_html__( 'Job Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'STANOWISKO',
			]
		);

		$repeater->add_control(
			'intro_text',
			[
				'label' => esc_html__( 'Intro Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
			]
		);

		$repeater->add_control(
			'text_left',
			[
				'label' => esc_html__( 'Text Left Column', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::WYSIWYG,
			]
		);

		$repeater->add_control(
			'text_right',
			[
				'label' => esc_html__( 'Text Right Column (Desktop)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::WYSIWYG,
			]
		);

		$this->add_control(
			'team_members',
			[
				'label' => esc_html__( 'Team Members', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$team_members = $settings['team_members'];

		if ( empty( $team_members ) ) {
			return;
		}

		?>
		<div class="kpg-team-slider-container">
			<!-- Header -->
			<div class="kpg-team-slider-header">
				<div class="kpg-team-slider-header-line">
					<h2 class="kpg-team-slider-title"><?php echo esc_html( $settings['title'] ); ?></h2>
				</div>
			</div>
			
			<!-- Images Section -->
			<div class="kpg-team-slider-images-section">
				<!-- Main Image Swiper -->
				<div class="kpg-team-slider-main-swiper swiper">
					<div class="swiper-wrapper">
						<?php foreach ( $team_members as $index => $member ) : ?>
							<div class="swiper-slide">
								<div class="kpg-team-slider-main-image">
									<?php 
									$main_image_url = ! empty( $member['main_image']['url'] ) ? $member['main_image']['url'] : ( ! empty( $member['image']['url'] ) ? $member['image']['url'] : '' );
									if ( ! empty( $main_image_url ) ) : ?>
										<img 
											src="<?php echo esc_url( $main_image_url ); ?>" 
											alt="<?php echo esc_attr( $member['name'] ); ?>"
											<?php if ( ! empty( $member['object_position'] ) ) : ?>
												data-object-position="<?php echo esc_attr( $member['object_position'] ); ?>"
											<?php endif; ?>
										>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				
				<!-- Thumbnails (desktop only) -->
				<div class="kpg-team-slider-thumbnails">
					<?php foreach ( $team_members as $index => $member ) : ?>
						<div class="kpg-team-slider-thumb <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo esc_attr( $index ); ?>">
							<?php if ( ! empty( $member['image']['url'] ) ) : ?>
								<img src="<?php echo esc_url( $member['image']['url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>">
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			
			<!-- Content Section -->
			<div class="kpg-team-slider-content-section">
				<?php foreach ( $team_members as $index => $member ) : ?>
					<div class="kpg-team-slider-slide-content <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-index="<?php echo esc_attr( $index ); ?>">
						<!-- Navigation (Mobile: first, Desktop: in bottom section) -->
						<div class="kpg-team-slider-navigation kpg-team-slider-navigation-mobile">
							<button class="kpg-team-slider-arrow-prev" aria-label="Previous">
								<svg class="kpg-team-slider-arrow-icon" width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(180deg);">
									<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
									<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
								</svg>
							</button>
							<button class="kpg-team-slider-arrow-next" aria-label="Next">
								<svg class="kpg-team-slider-arrow-icon" width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
									<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
								</svg>
							</button>
						</div>
						
						<!-- Top Section: Name, Job Title, Intro Text -->
						<div class="kpg-team-slider-top-content">
							<div class="kpg-team-slider-name-section">
								<h3 class="kpg-team-slider-name"><?php echo esc_html( $member['name'] ); ?></h3>
								<span class="kpg-team-slider-job-title"><?php echo esc_html( $member['job_title'] ); ?></span>
							</div>
							
							<?php if ( ! empty( $member['intro_text'] ) ) : ?>
								<div class="kpg-team-slider-intro-text">
									<?php echo wp_kses_post( wpautop( $member['intro_text'] ) ); ?>
								</div>
							<?php endif; ?>
						</div>
						
						<!-- Bottom Section: Navigation and Text Columns -->
						<div class="kpg-team-slider-bottom-content">
							<!-- Navigation -->
							<div class="kpg-team-slider-navigation">
								<button class="kpg-team-slider-arrow-prev" aria-label="Previous">
									<svg class="kpg-team-slider-arrow-icon" width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(180deg);">
										<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
										<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
									</svg>
								</button>
								<button class="kpg-team-slider-arrow-next" aria-label="Next">
									<svg class="kpg-team-slider-arrow-icon" width="40" height="32" viewBox="0 0 40 32" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
										<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
									</svg>
								</button>
							</div>
							
							<!-- Text Columns -->
							<div class="kpg-team-slider-text-columns">
								<?php if ( ! empty( $member['text_left'] ) ) : ?>
									<div class="kpg-team-slider-text kpg-team-slider-text-left">
										<div class="kpg-team-slider-text-content">
											<?php echo wp_kses_post( $member['text_left'] ); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $member['text_right'] ) ) : ?>
									<div class="kpg-team-slider-text kpg-team-slider-text-right">
										<div class="kpg-team-slider-text-content">
											<?php echo wp_kses_post( $member['text_right'] ); ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
							
							<!-- See More Button (Mobile only) -->
							<div class="kpg-team-slider-see-more-wrapper">
								<button class="kpg-team-slider-see-more-btn" type="button" aria-expanded="false">
									<span class="kpg-team-slider-see-more-text">zobacz więcej</span>
								</button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

