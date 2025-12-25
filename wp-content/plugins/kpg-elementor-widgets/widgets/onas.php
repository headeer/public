<?php
/**
 * KPG O Nas Widget
 *
 * @package KPG_Elementor_Widgets
 * 
 * Wyświetla sekcję "O nas" z edytowalnymi tekstami
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Onas_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-onas';
	}

	public function get_title() {
		return esc_html__( 'KPG O Nas', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-text';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'o nas', 'about', 'us', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-onas-style' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'header_section',
			[
				'label' => esc_html__( 'Header', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'O NAS',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'mission_section',
			[
				'label' => esc_html__( 'Mission Section', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'section_number',
			[
				'label' => esc_html__( 'Section Number', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => '1.0',
			]
		);

		$this->add_control(
			'mission_text',
			[
				'label' => esc_html__( 'Mission Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => 'Naszą misją jest tworzenie przestrzeni, w której prawo staje się narzędziem wsparcia, rozwoju i innowacji. Wierzymy, że poprzez pomoc prawną możemy aktywnie wpływać na kształtowanie lepszej przyszłości – zarówno w sferze edukacji, jak i codziennego funkcjonowania naszych klientów.',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'education_section',
			[
				'label' => esc_html__( 'Education Section', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'education_text',
			[
				'label' => esc_html__( 'Education Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => 'Dążymy do usprawnienia systemu edukacji w Polsce. Wspieramy jednostki samorządu terytorialnego, szkoły, przedszkola oraz inne instytucje edukacyjne i społeczne. Pomagamy im nie tylko w sprawach formalnych, ale także w budowaniu nowych, alternatywnych rozwiązań, które zmieniają sposób myślenia o nauczaniu i wychowaniu.<br><br>Działamy z wiarą, że edukacja jest podstawą do budowania świadomego, rozwiniętego społeczeństwa. Nasze działania są ukierunkowane na realne wspieranie tych, którzy chcą zmieniać świat poprzez naukę i rozwój.',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'quote_section',
			[
				'label' => esc_html__( 'Quote Section', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'quote_icon',
			[
				'label' => esc_html__( 'Quote Icon', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'quote_text',
			[
				'label' => esc_html__( 'Quote Text', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
				'default' => 'Wierzymy, że ciągłe pogłębianie wiedzy i intuicja prawników oparta na doświadczeniu i praktyce, zapewnią klientom KPGiO bezpieczeństwo prawne i powodzenie.',
			]
		);

		$this->add_control(
			'author_avatar',
			[
				'label' => esc_html__( 'Author Avatar', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'author_name',
			[
				'label' => esc_html__( 'Author Name', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Daria Bezwińska',
			]
		);

		$this->add_control(
			'author_title',
			[
				'label' => esc_html__( 'Author Title', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'ADWOKAT',
			]
		);

		$this->add_control(
			'quote_image',
			[
				'label' => esc_html__( 'Quote Image', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
			]
		);

		$this->add_control(
			'quote_image_mobile',
			[
				'label' => esc_html__( 'Quote Image (Mobile)', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'description' => esc_html__( 'Image displayed below quote frame on mobile devices', 'kpg-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		?>
		<div class="kpg-onas-container">
			<div class="kpg-onas-wrapper">
				<!-- Header -->
				<div class="kpg-onas-header">
					<div class="kpg-onas-title-wrapper">
						<h2 class="kpg-onas-title"><?php echo esc_html( $settings['title'] ); ?></h2>
					</div>
				</div>

				<!-- Mission Section -->
				<div class="kpg-onas-mission-section">
					<div class="kpg-onas-mission-content">
						<span class="kpg-onas-mission-text"><?php echo wp_kses_post( wpautop( $settings['mission_text'] ) ); ?></span>
						<span class="kpg-onas-section-number"><?php echo esc_html( $settings['section_number'] ); ?></span>
					</div>
				</div>

				<!-- Education Section -->
				<div class="kpg-onas-education-section">
					<div class="kpg-onas-education-content">
						<div class="kpg-onas-education-text">
							<?php echo wp_kses_post( $settings['education_text'] ); ?>
						</div>
					</div>
				</div>

				<!-- Quote Section -->
				<div class="kpg-onas-quote-section">
					<svg class="kpg-onas-quote-icon-svg-desktop" xmlns="http://www.w3.org/2000/svg" width="109" height="96" viewBox="0 0 109 96" fill="none">
						<path d="M7.62939e-06 96L6.18737e-06 79.5052C16.7946 75.2165 25.0272 65.3196 25.3565 44.5361L3.13028e-06 44.5361L-7.63193e-07 1.89969e-06L45.4441 -2.07316e-06L45.4441 33.6495C45.4441 70.9279 34.2477 88.7423 7.62939e-06 96ZM63.5559 96L63.5559 79.5052C80.3505 75.2165 88.5831 65.3196 88.9124 44.5361L63.5559 44.5361L63.5559 -3.65654e-06L109 -7.62939e-06L109 33.6495C109 70.9278 97.8036 88.7423 63.5559 96Z" fill="#D4DDE0"/>
					</svg>
					
					<!-- Desktop: Quote Content Wrapper -->
					<div class="kpg-onas-quote-content-wrapper">
						<div class="kpg-onas-quote-frame">
							<div class="kpg-onas-quote-text-wrapper">
								<div class="kpg-onas-quote-text">
									<?php echo wp_kses_post( wpautop( $settings['quote_text'] ) ); ?>
								</div>
								
								<div class="kpg-onas-author-info">
									<?php if ( ! empty( $settings['author_avatar']['url'] ) ) : ?>
										<div class="kpg-onas-author-avatar">
											<img src="<?php echo esc_url( $settings['author_avatar']['url'] ); ?>" alt="<?php echo esc_attr( $settings['author_name'] ); ?>">
										</div>
									<?php endif; ?>
									<div class="kpg-onas-author-details">
										<span class="kpg-onas-author-name"><?php echo esc_html( $settings['author_name'] ); ?></span>
										<span class="kpg-onas-author-title"><?php echo esc_html( $settings['author_title'] ); ?></span>
									</div>
								</div>
							</div>
						</div>
						
						<?php if ( ! empty( $settings['quote_image']['url'] ) ) : ?>
							<div class="kpg-onas-quote-image">
								<img src="<?php echo esc_url( $settings['quote_image']['url'] ); ?>" alt="Quote Image">
							</div>
						<?php endif; ?>
					</div>

					<!-- Mobile: Quote Frame (direct in section) -->
					<div class="kpg-onas-quote-frame">
						<svg class="kpg-onas-quote-icon-svg" xmlns="http://www.w3.org/2000/svg" width="73" height="64" viewBox="0 0 73 64" fill="none">
							<path d="M7.62939e-06 64L6.66805e-06 53.0035C11.2477 50.1443 16.7613 43.5464 16.9819 29.6907L4.62998e-06 29.6907L2.03434e-06 -1.24753e-06L30.435 -3.90825e-06L30.435 22.433C30.435 47.2852 22.9366 59.1615 7.62939e-06 64ZM42.565 64L42.565 53.0035C53.8127 50.1443 59.3263 43.5464 59.5468 29.6907L42.565 29.6907L42.5649 -4.96868e-06L73 -7.62939e-06L73 22.433C73 47.2852 65.5015 59.1615 42.565 64Z" fill="#D4DDE0"/>
						</svg>
						<div class="kpg-onas-quote-text-wrapper">
							<div class="kpg-onas-quote-text">
								<?php echo wp_kses_post( wpautop( $settings['quote_text'] ) ); ?>
							</div>
							
							<div class="kpg-onas-author-wrapper">
								<?php if ( ! empty( $settings['author_avatar']['url'] ) ) : ?>
									<div class="kpg-onas-author-avatar">
										<img src="<?php echo esc_url( $settings['author_avatar']['url'] ); ?>" alt="<?php echo esc_attr( $settings['author_name'] ); ?>">
									</div>
								<?php endif; ?>
								<div class="kpg-onas-author-info">
									<div class="kpg-onas-author-details">
										<span class="kpg-onas-author-name"><?php echo esc_html( $settings['author_name'] ); ?></span>
										<span class="kpg-onas-author-title"><?php echo esc_html( $settings['author_title'] ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Quote Image Mobile (below quote frame) -->
				<?php if ( ! empty( $settings['quote_image_mobile']['url'] ) ) : ?>
					<div class="kpg-onas-quote-image-mobile">
						<img src="<?php echo esc_url( $settings['quote_image_mobile']['url'] ); ?>" alt="Quote Image Mobile">
					</div>
				<?php endif; ?>

				<!-- Quote Image (separate on mobile) -->
				<?php if ( ! empty( $settings['quote_image']['url'] ) ) : ?>
					<div class="kpg-onas-quote-image">
						<img src="<?php echo esc_url( $settings['quote_image']['url'] ); ?>" alt="Quote Image">
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

