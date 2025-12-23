<?php
/**
 * KPG Blog Archive Desktop Widget
 * 
 * 3 KOLUMNY GRID - dokładnie z Figmy
 * Prompt: 3 columns, 6 rows, equal heights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Blog_Archive_Desktop_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-blog-archive-desktop';
	}

	public function get_title() {
		return esc_html__( 'KPG Blog Archive Desktop', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_style_depends() {
		return [ 'kpg-blog-archive-desktop-style', 'kpg-pagination-style' ];
	}

	public function get_script_depends() {
		return [ 'kpg-pagination-script' ];
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
			'posts_per_page',
			[
				'label' => esc_html__( 'Posts Per Page', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 18,
				'min' => 3,
				'max' => 50,
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__( 'Show Pagination', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$paged = max( 1, get_query_var( 'paged' ) );
		$sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'newest';
		$order = ( $sort === 'oldest' ) ? 'ASC' : 'DESC';

		$args = [
			'post_type' => 'post',
			'posts_per_page' => intval( $settings['posts_per_page'] ),
			'paged' => $paged,
			'orderby' => 'date',
			'order' => $order,
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
		];

		$query = new WP_Query( $args );
		?>
		
		<div class="kpg-blog-desktop-container">
			<?php if ( $query->have_posts() ) : ?>
				<div class="kpg-blog-desktop-grid">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<article class="kpg-blog-desktop-item">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="kpg-blog-desktop-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'medium' ); ?>
									</a>
								</div>
							<?php else : ?>
								<div class="kpg-blog-desktop-image">
									<a href="<?php the_permalink(); ?>">
										<div class="kpg-blog-desktop-image-placeholder"></div>
									</a>
								</div>
							<?php endif; ?>
							
							<h2 class="kpg-blog-desktop-title">
								<a href="<?php the_permalink(); ?>">
									<?php the_title(); ?>
								</a>
							</h2>
							
							<div class="kpg-blog-desktop-excerpt">
								<div class="kpg-blog-desktop-excerpt-inner">
									<?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?>
								</div>
							</div>
							
							<div class="kpg-blog-desktop-meta">
								<div class="kpg-blog-desktop-avatar">
									<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
								</div>
								<span class="kpg-blog-desktop-author-date">
									<?php 
									$first = get_the_author_meta( 'first_name' );
									$last = get_the_author_meta( 'last_name' );
									$name = trim( $first . ' ' . $last );
									if ( empty( $name ) ) {
										$name = get_the_author();
									}
									echo esc_html( $name . ' • ' . get_the_date( 'd/m/y' ) );
									?>
								</span>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			<?php endif; ?>
			
			<?php if ( $settings['show_pagination'] === 'yes' && $query->max_num_pages > 1 ) : 
				$this->render_pagination( $paged, $query->max_num_pages );
			endif; ?>
		</div>
		<?php
	}

	protected function render_pagination( $current, $max ) {
		$pages = [];
		for ( $i = 1; $i <= min( 3, $max ); $i++ ) {
			$pages[] = [ 'number' => $i, 'is_separator' => false ];
		}
		if ( $max > 3 ) {
			$pages[] = [ 'is_separator' => true ];
			if ( $current > 3 && $current < $max ) {
				$pages[] = [ 'number' => $current, 'is_separator' => false ];
				if ( $current < ( $max - 1 ) ) {
					$pages[] = [ 'is_separator' => true ];
				}
			}
			$pages[] = [ 'number' => $max, 'is_separator' => false ];
		}
		?>
		<div class="kpg-blog-separator"></div>
		<div class="kpg-blog-pagination">
			<div class="kpg-blog-pagination-numbers">
				<?php foreach ( $pages as $page ) : ?>
					<?php if ( isset( $page['is_separator'] ) && $page['is_separator'] ) : ?>
						<div class="kpg-blog-pagination-separator"></div>
					<?php else : ?>
						<span class="kpg-blog-pagination-item <?php echo ( $page['number'] === $current ) ? 'active' : 'inactive'; ?>" 
							  data-page="<?php echo esc_attr( $page['number'] ); ?>">
							<?php echo str_pad( $page['number'], 2, '0', STR_PAD_LEFT ); ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="kpg-blog-pagination-arrow <?php echo ( $current >= $max ) ? 'disabled' : ''; ?>" data-direction="next">
				<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
					<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
					<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
				</svg>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="kpg-blog-desktop-container">
			<div class="kpg-blog-desktop-grid">
				<# for (var i = 0; i < 6; i++) { #>
					<article class="kpg-blog-desktop-item">
						<div class="kpg-blog-desktop-image" style="background: #e3ebec;"></div>
						<h2 class="kpg-blog-desktop-title"><a href="#">Post Title</a></h2>
						<div class="kpg-blog-desktop-excerpt">
							<div class="kpg-blog-desktop-excerpt-inner">Excerpt text...</div>
						</div>
						<div class="kpg-blog-desktop-meta">
							<div class="kpg-blog-desktop-avatar" style="background: #ccc;"></div>
							<span class="kpg-blog-desktop-author-date">Author • 23/12/25</span>
						</div>
					</article>
				<# } #>
			</div>
			<div class="kpg-blog-separator"></div>
			<div class="kpg-blog-pagination">
				<div class="kpg-blog-pagination-numbers">
					<span class="kpg-blog-pagination-item active">01</span>
					<span class="kpg-blog-pagination-item inactive">02</span>
				</div>
				<div class="kpg-blog-pagination-arrow">
					<svg xmlns="http://www.w3.org/2000/svg" width="40" height="32" viewBox="0 0 40 32" fill="none">
						<path d="M20 24L28 16L20 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
						<path d="M12 24L20 16L12 8" stroke="#252B2B" stroke-width="1.33333" stroke-linecap="square"/>
					</svg>
				</div>
			</div>
		</div>
		<?php
	}
}

