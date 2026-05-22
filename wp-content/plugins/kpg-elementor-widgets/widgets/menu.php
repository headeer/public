<?php
/**
 * KPG Menu Widget
 *
 * @package KPG_Elementor_Widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
	return;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class KPG_Elementor_Menu_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-menu';
	}

	public function get_title() {
		return esc_html__( 'KPG Menu', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-nav-menu';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_keywords() {
		return [ 'menu', 'navigation', 'mobile menu', 'desktop menu', 'kpg' ];
	}

	public function get_style_depends() {
		return [ 'kpg-menu-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-menu-script', 'kpg-mobile-menu-anchor-close-script' ];
	}

	protected function register_controls() {
		$menus = wp_get_nav_menus();
		$options = [];

		foreach ( $menus as $menu ) {
			$options[ (string) $menu->term_id ] = $menu->name;
		}

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'kpg-elementor-widgets' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_id',
			[
				'label' => esc_html__( 'WordPress Menu', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => ! empty( $options ) ? (string) array_key_first( $options ) : '',
			]
		);

		$this->add_control(
			'mobile_toggle_label',
			[
				'label' => esc_html__( 'Mobile Toggle Label', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'MENU', 'kpg-elementor-widgets' ),
			]
		);

		$this->add_control(
			'show_mobile_toggle',
			[
				'label' => esc_html__( 'Show Mobile Toggle', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'kpg-elementor-widgets' ),
				'label_off' => esc_html__( 'No', 'kpg-elementor-widgets' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	private static function build_tree( $menu_items ) {
		$nodes = [];
		$roots = [];

		foreach ( $menu_items as $item ) {
			$nodes[ (int) $item->ID ] = [
				'item' => $item,
				'children' => [],
			];
		}

		foreach ( $menu_items as $item ) {
			$id = (int) $item->ID;
			$parent = (int) $item->menu_item_parent;

			if ( $parent > 0 && isset( $nodes[ $parent ] ) ) {
				$nodes[ $parent ]['children'][] = &$nodes[ $id ];
			} else {
				$roots[] = &$nodes[ $id ];
			}
		}

		return $roots;
	}

	private static function normalize_url( $url ) {
		$url = (string) $url;
		if ( $url === '' || $url === '#' ) {
			return '';
		}
		return esc_url( $url );
	}

	private static function is_current_item( $item ) {
		if ( empty( $item->classes ) || ! is_array( $item->classes ) ) {
			return false;
		}

		$current_classes = [
			'current-menu-item',
			'current-menu-parent',
			'current-menu-ancestor',
			'current_page_item',
			'current_page_parent',
			'current_page_ancestor',
		];

		return count( array_intersect( $current_classes, $item->classes ) ) > 0;
	}

	private static function render_link_markup( $label, $url, $class_name, $is_current = false ) {
		if ( $url === '' ) {
			echo '<span class="' . esc_attr( $class_name ) . '">' . esc_html( $label ) . '</span>';
			return;
		}

		$current_attr = $is_current ? ' aria-current="page"' : '';
		echo '<a class="' . esc_attr( $class_name ) . '" href="' . esc_url( $url ) . '" itemprop="url"' . $current_attr . '>';
		echo '<span itemprop="name">' . esc_html( $label ) . '</span>';
		echo '</a>';
	}

	private static function render_brand_logo() {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="127" height="30" viewBox="0 0 127 30" fill="none" role="img" aria-hidden="true" focusable="false">
			<path d="M11.7642 14.4108C15.2916 14.4108 18.7484 10.6136 19.8482 9.53853L20.2599 9.94203C19.163 11.0171 15.2901 14.408 15.2916 17.868V29.3967H22.3492V14.4094C22.3463 10.3729 19.9937 7.49219 15.8797 7.49219H0V14.4094H11.7628L11.7642 14.4108Z" fill="#2D3535"/>
			<path d="M0.00195312 21.9002C7.64774 21.9002 7.58158 21.5529 9.76357 19.4143L10.187 19.8294C8.00798 21.965 7.64627 21.8988 7.64627 29.3982H14.701C14.701 21.439 8.11972 14.9873 0.00195312 14.9873V21.9002Z" fill="#2D3535"/>
			<path d="M12.939 6.91721C22.933 6.91721 28.7467 0.814213 29.1628 0.406386L29.5789 0.814213C29.1628 1.22204 22.9344 6.91577 22.9374 16.7166V29.3981H29.995V0H0V6.91721H12.939Z" fill="#2D3535"/>
			<path d="M45.8258 14.9439L59.3706 25.9465H54.2435L42.7763 16.4152V25.9465H39.4062V3.45264H42.7763V13.6843L53.7612 3.45264H58.4869L45.8243 14.9424L45.8258 14.9439Z" fill="#2D3535"/>
			<path d="M59.3633 3.45264H70.0042C74.8034 3.45264 78.2293 6.46451 78.2293 10.6797C78.2293 14.8949 74.7872 17.9629 70.0042 17.9629H62.7348V25.9451H59.3648V3.45264H59.3633ZM69.7395 14.8963C72.8684 14.8963 74.8592 13.2607 74.8592 10.6883C74.8592 8.11599 72.9007 6.52071 69.8042 6.52071H62.7348V14.8963H69.7395Z" fill="#2D3535"/>
			<path d="M104.468 25.9465H101.098V3.45264H104.468V25.9451V25.9465Z" fill="#2D3535"/>
			<path d="M116.16 2.93408C122.603 2.93408 127.001 7.67669 127.001 14.653C127.001 21.6293 122.603 26.3719 116.16 26.3719C109.717 26.3719 105.326 21.6221 105.326 14.653C105.326 7.6839 109.715 2.93408 116.16 2.93408ZM116.16 23.3038C120.822 23.3038 123.63 20.0556 123.63 14.653C123.63 9.25036 120.822 6.00215 116.16 6.00215C111.497 6.00215 108.698 9.25036 108.698 14.653C108.698 20.0556 111.499 23.3038 116.16 23.3038Z" fill="#2D3535"/>
			<path d="M88.5809 13.9135V16.9024H96.5163C96.3634 20.5987 93.1625 23.3901 88.8852 23.3901C84.336 23.3901 81.6879 20.1189 81.6879 14.7004C81.6879 9.28189 84.4889 6.04954 89.1425 6.04954C92.6978 6.04954 95.2651 7.92151 96.059 11.2403H99.4291C98.4425 6.16771 94.4064 2.98291 89.1337 2.98291C82.698 2.98291 78.3164 7.73273 78.3164 14.7018C78.3164 21.6709 82.3128 26.4207 87.953 26.4207C92.1817 26.4207 95.4312 23.7072 96.5237 19.6015V19.6491L96.6531 19.2139L97.2236 19.3781L96.5237 21.7271V25.9495H99.7334V13.9164H88.5794L88.5809 13.9135Z" fill="#2D3535"/>
		</svg>
		<?php
	}

	private static function render_brand_logo_mobile() {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="90" height="21" viewBox="0 0 90 21" fill="none" role="img" aria-hidden="true" focusable="false">
			<path d="M8.33682 10.2943C10.8365 10.2943 13.2862 7.58176 14.0656 6.81382L14.3573 7.10205C13.58 7.86999 10.8355 10.2922 10.8365 12.7638V20.9991H15.838V10.2932C15.8359 7.40984 14.1687 5.35205 11.2533 5.35205H0V10.2932H8.33578L8.33682 10.2943Z" fill="#404848"/>
			<path d="M0.000976562 15.6441C5.41924 15.6441 5.37235 15.3961 6.91863 13.8684L7.21872 14.1649C5.67452 15.6905 5.41819 15.6431 5.41819 21.0002H10.4176C10.4176 15.3147 5.75371 10.7061 0.000976562 10.7061V15.6441Z" fill="#404848"/>
			<path d="M9.16936 4.94118C16.2516 4.94118 20.3716 0.581618 20.6665 0.290294L20.9614 0.581618C20.6665 0.872941 16.2527 4.94015 16.2548 11.9412V21H21.2562V0H0V4.94118H9.16936Z" fill="#404848"/>
			<path d="M32.475 10.6748L42.0737 18.5344H38.4403L30.314 11.7259V18.5344H27.9258V2.46631H30.314V9.77513L38.0986 2.46631H41.4475L32.474 10.6738L32.475 10.6748Z" fill="#404848"/>
			<path d="M42.0674 2.46631H49.6081C53.0091 2.46631 55.4369 4.61778 55.4369 7.62881C55.4369 10.6398 52.9977 12.8315 49.6081 12.8315H44.4566V18.5334H42.0684V2.46631H42.0674ZM49.4206 10.6409C51.6379 10.6409 53.0487 9.47248 53.0487 7.63499C53.0487 5.79749 51.6608 4.65793 49.4664 4.65793H44.4566V10.6409H49.4206Z" fill="#404848"/>
			<path d="M74.0308 18.5344H71.6426V2.46631H74.0308V18.5334V18.5344Z" fill="#404848"/>
			<path d="M82.3179 2.0957C86.8838 2.0957 90.0003 5.4835 90.0003 10.4669C90.0003 15.4503 86.8838 18.8381 82.3179 18.8381C77.752 18.8381 74.6406 15.4451 74.6406 10.4669C74.6406 5.48864 77.7509 2.0957 82.3179 2.0957ZM82.3179 16.6464C85.622 16.6464 87.6121 14.3261 87.6121 10.4669C87.6121 6.60761 85.622 4.28732 82.3179 4.28732C79.0138 4.28732 77.0299 6.60761 77.0299 10.4669C77.0299 14.3261 79.0148 16.6464 82.3179 16.6464Z" fill="#404848"/>
			<path d="M62.774 9.93895V12.0739H68.3975C68.2892 14.7144 66.0208 16.7084 62.9897 16.7084C59.7658 16.7084 57.8892 14.3716 57.8892 10.501C57.8892 6.63042 59.8742 4.32145 63.172 4.32145C65.6915 4.32145 67.5108 5.65865 68.0735 8.02939H70.4617C69.7625 4.40586 66.9023 2.13086 63.1658 2.13086C58.6051 2.13086 55.5 5.5238 55.5 10.502C55.5 15.4803 58.3321 18.8732 62.3291 18.8732C65.3258 18.8732 67.6286 16.9348 68.4027 14.002V14.036L68.4944 13.7251L68.8987 13.8425L68.4027 15.5204V18.5366H70.6774V9.941H62.773L62.774 9.93895Z" fill="#404848"/>
		</svg>
		<?php
	}

	private static function render_desktop_level( $nodes, $depth = 0, $number_prefix = '' ) {
		if ( empty( $nodes ) ) {
			return;
		}

		$list_class = $depth === 0 ? 'kpg-menu-desktop-list' : 'kpg-menu-desktop-submenu';
		echo '<ul class="' . esc_attr( $list_class ) . '">';

		foreach ( $nodes as $index => $node ) {
			$item = $node['item'];
			$children = $node['children'];
			$has_children = ! empty( $children );
			$label = $item->title ?: '';
			$url = self::normalize_url( $item->url );
			$is_current = self::is_current_item( $item );
			$item_id = 'kpg-menu-item-' . (int) $item->ID;
			$is_top_level = $depth === 0;
			$current_position = (int) $index + 1;
			$menu_number = $is_top_level ? (string) $current_position . '.0' : ( $number_prefix !== '' ? $number_prefix . '.' . $current_position : (string) $current_position );
			$children_prefix = $is_top_level ? (string) $current_position : $menu_number;

			echo '<li class="kpg-menu-item' . ( $has_children ? ' has-children' : '' ) . ( $is_top_level ? ' is-top-level' : '' ) . '" itemprop="name">';

			if ( $has_children ) {
				echo '<div class="kpg-menu-desktop-item-row">';
				echo '<span class="kpg-menu-index" aria-hidden="true">' . esc_html( $menu_number ) . '</span>';
				self::render_link_markup( $label, $url, 'kpg-menu-desktop-link', $is_current );
				echo '<button class="kpg-menu-desktop-trigger" type="button" aria-expanded="false" aria-haspopup="true" aria-controls="' . esc_attr( $item_id ) . '">';
				echo '<span class="kpg-menu-plus-icon" aria-hidden="true">';
				echo '<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" focusable="false">';
				echo '<path class="kpg-menu-plus-horizontal" d="M0.666016 4.6665H7.66602" stroke="#2D3535" stroke-width="1.33333" stroke-linecap="square"/>';
				echo '<path class="kpg-menu-plus-vertical" d="M4.16602 1.1665V8.1665" stroke="#2D3535" stroke-width="1.33333" stroke-linecap="square"/>';
				echo '</svg>';
				echo '</span>';
				echo '<span class="screen-reader-text">' . esc_html__( 'Open submenu', 'kpg-elementor-widgets' ) . '</span>';
				echo '</button>';
				echo '</div>';
				echo '<div class="kpg-menu-desktop-dropdown is-collapsed" id="' . esc_attr( $item_id ) . '">';
				self::render_desktop_level( $children, $depth + 1, $children_prefix );
				echo '</div>';
			} else {
				echo '<div class="kpg-menu-desktop-item-row">';
				echo '<span class="kpg-menu-index" aria-hidden="true">' . esc_html( $menu_number ) . '</span>';
				self::render_link_markup( $label, $url, 'kpg-menu-desktop-link', $is_current );
				echo '</div>';
			}

			echo '</li>';
		}

		echo '</ul>';
	}

	private static function render_mobile_level( $nodes, $depth = 0, $number_prefix = '' ) {
		if ( empty( $nodes ) ) {
			return;
		}

		echo '<ul class="kpg-menu-mobile-list level-' . (int) $depth . '">';

		foreach ( $nodes as $index => $node ) {
			$item = $node['item'];
			$children = $node['children'];
			$has_children = ! empty( $children );
			$label = $item->title ?: '';
			$url = self::normalize_url( $item->url );
			$is_current = self::is_current_item( $item );
			$submenu_id = 'kpg-mobile-submenu-' . (int) $item->ID;
			$current_position = (int) $index + 1;
			$menu_number = $number_prefix !== '' ? $number_prefix . '.' . $current_position : (string) $current_position;
			$children_prefix = $depth === 0 ? (string) $current_position : $menu_number;

			echo '<li class="kpg-menu-mobile-item' . ( $has_children ? ' has-children' : '' ) . '">';

			if ( $has_children ) {
				echo '<div class="kpg-menu-mobile-item-row">';
				if ( $depth > 0 ) {
					echo '<span class="kpg-menu-mobile-index" aria-hidden="true">' . esc_html( $menu_number ) . '</span>';
				}
				self::render_link_markup( $label, $url, 'kpg-menu-mobile-link', $is_current );
				echo '<button class="kpg-menu-mobile-submenu-toggle" type="button" aria-expanded="false" aria-haspopup="true" aria-controls="' . esc_attr( $submenu_id ) . '">';
				echo '<span class="kpg-menu-mobile-plus-icon" aria-hidden="true"><span class="kpg-menu-mobile-plus-h"></span><span class="kpg-menu-mobile-plus-v"></span></span>';
				echo '<span class="screen-reader-text">' . esc_html__( 'Toggle submenu', 'kpg-elementor-widgets' ) . '</span>';
				echo '</button>';
				echo '</div>';
				echo '<div class="kpg-menu-mobile-submenu is-collapsed" id="' . esc_attr( $submenu_id ) . '">';
				self::render_mobile_level( $children, $depth + 1, $children_prefix );
				echo '</div>';
			} else {
				if ( $depth > 0 ) {
					echo '<div class="kpg-menu-mobile-item-row">';
					echo '<span class="kpg-menu-mobile-index" aria-hidden="true">' . esc_html( $menu_number ) . '</span>';
					self::render_link_markup( $label, $url, 'kpg-menu-mobile-link', $is_current );
					echo '</div>';
				} else {
					self::render_link_markup( $label, $url, 'kpg-menu-mobile-link', $is_current );
				}
			}

			echo '</li>';
		}

		echo '</ul>';
	}

	public static function render_standalone( $menu_id, $args = [] ) {
		$menu_id = (int) $menu_id;
		if ( $menu_id <= 0 ) {
			return;
		}

		$menu_items = wp_get_nav_menu_items( $menu_id, [ 'update_post_term_cache' => false ] );
		if ( empty( $menu_items ) || ! is_array( $menu_items ) ) {
			return;
		}

		$tree = self::build_tree( $menu_items );
		$mobile_toggle_label = isset( $args['mobile_toggle_label'] ) ? (string) $args['mobile_toggle_label'] : 'MENU';
		$show_mobile_toggle = ! isset( $args['show_mobile_toggle'] ) || (bool) $args['show_mobile_toggle'];
		$widget_id = isset( $args['widget_id'] ) ? (string) $args['widget_id'] : 'kpg-menu-' . wp_rand( 1000, 99999 );
		$extra_class = isset( $args['extra_class'] ) ? (string) $args['extra_class'] : '';
		?>
		<nav class="kpg-menu-widget <?php echo esc_attr( $extra_class ); ?>" id="<?php echo esc_attr( $widget_id ); ?>" aria-label="<?php esc_attr_e( 'Main navigation', 'kpg-elementor-widgets' ); ?>" itemscope itemtype="https://schema.org/SiteNavigationElement">
			<div class="kpg-menu-desktop" aria-hidden="false">
				<div class="kpg-menu-desktop-row">
					<a class="kpg-menu-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Strona główna', 'kpg-elementor-widgets' ); ?>">
						<?php self::render_brand_logo(); ?>
					</a>

					<div class="kpg-menu-desktop-shell">
						<div class="kpg-menu-desktop-main">
							<?php self::render_desktop_level( $tree, 0 ); ?>
						</div>

						<div class="kpg-menu-desktop-meta">
							<span class="kpg-menu-desktop-divider" aria-hidden="true"></span>
							<div class="kpg-menu-desktop-email">
								<span class="kpg-menu-meta-label">E:</span>
								<a class="kpg-menu-meta-value" href="mailto:kancelaria@kpgio.pl">KANCELARIA@KPGIO.PL</a>
							</div>
						</div>
					</div>
					<div class="kpg-menu-desktop-phone">
						<span class="kpg-menu-meta-label">T:</span>
						<a class="kpg-menu-meta-value" href="tel:+48533940018">+48 533 940 018</a>
					</div>
				</div>
			</div>

			<div class="kpg-menu-mobile" aria-hidden="false">
				<?php if ( $show_mobile_toggle ) : ?>
					<div class="kpg-menu-mobile-bar">
						<a class="kpg-menu-mobile-home" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Strona główna', 'kpg-elementor-widgets' ); ?>">
							<span class="kpg-menu-mobile-toggle-brand" aria-hidden="true"><?php self::render_brand_logo_mobile(); ?></span>
						</a>
						<button class="kpg-menu-mobile-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $widget_id ); ?>-panel">
							<span class="kpg-menu-mobile-toggle-action">
								<span class="kpg-menu-mobile-toggle-label"><?php echo esc_html( $mobile_toggle_label ); ?></span>
								<span class="kpg-menu-mobile-toggle-hamburger" aria-hidden="true">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
										<path d="M2 8H14" stroke="#A3AFB0" stroke-width="1.33333" stroke-linecap="square"/>
										<path d="M2 4H14" stroke="#A3AFB0" stroke-width="1.33333" stroke-linecap="square"/>
										<path d="M2 12H14" stroke="#A3AFB0" stroke-width="1.33333" stroke-linecap="square"/>
									</svg>
								</span>
							</span>
						</button>
					</div>
				<?php endif; ?>
				<div class="kpg-menu-mobile-panel <?php echo $show_mobile_toggle ? 'is-collapsed' : 'is-open'; ?>" id="<?php echo esc_attr( $widget_id ); ?>-panel">
					<div class="kpg-menu-mobile-panel-header">
						<a class="kpg-menu-mobile-panel-home" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Strona główna', 'kpg-elementor-widgets' ); ?>">
							<span class="kpg-menu-mobile-panel-brand" aria-hidden="true"><?php self::render_brand_logo_mobile(); ?></span>
						</a>
						<button class="kpg-menu-mobile-close" type="button" aria-label="<?php esc_attr_e( 'Zamknij menu', 'kpg-elementor-widgets' ); ?>">
							<span class="kpg-menu-mobile-close-label"><?php esc_html_e( 'ZAMKNIJ', 'kpg-elementor-widgets' ); ?></span>
							<span class="kpg-menu-mobile-close-icon" aria-hidden="true"></span>
						</button>
					</div>
					<?php self::render_mobile_level( $tree, 0, '' ); ?>
					<div class="kpg-menu-mobile-contact">
						<p class="kpg-menu-mobile-contact-line">E: kancelaria@kpgio.pl</p>
						<p class="kpg-menu-mobile-contact-line">T: +48 533 940 018</p>
					</div>
				</div>
			</div>
		</nav>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$menu_id = isset( $settings['menu_id'] ) ? (int) $settings['menu_id'] : 0;

		if ( $menu_id <= 0 ) {
			return;
		}

		$mobile_toggle_label = isset( $settings['mobile_toggle_label'] ) ? $settings['mobile_toggle_label'] : 'MENU';
		$show_mobile_toggle = isset( $settings['show_mobile_toggle'] ) && $settings['show_mobile_toggle'] === 'yes';
		$widget_id = 'kpg-menu-' . $this->get_id();
		self::render_standalone(
			$menu_id,
			[
				'mobile_toggle_label' => $mobile_toggle_label,
				'show_mobile_toggle'  => $show_mobile_toggle,
				'widget_id'           => $widget_id,
			]
		);
	}
}
