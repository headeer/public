<?php
/**
 * KPG Blog Archive Mobile Widget
 * 
 * LISTA PIONOWA z separatorami - dokładnie z Figmy
 * Regular posts + Large posts (co 3.) + Pagination
 * BEZ featured post (usunięty na życzenie)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class KPG_Elementor_Blog_Archive_Mobile_Widget extends Widget_Base {

	public function get_name() {
		return 'kpg-blog-archive-mobile';
	}

	public function get_title() {
		return esc_html__( 'KPG Blog Archive Mobile', 'kpg-elementor-widgets' );
	}

	public function get_icon() {
		return 'eicon-posts-group';
	}

	public function get_categories() {
		return [ 'kpg-widgets' ];
	}

	public function get_style_depends() {
		return [ 'kpg-blog-archive-mobile-style', 'kpg-pagination-style' ];
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
				'default' => 10,
				'min' => 1,
				'max' => 50,
			]
		);

		$this->add_control(
			'large_post_interval',
			[
				'label' => esc_html__( 'Large Post Interval', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3,
				'description' => esc_html__( 'Every Nth post = large format (0 = disable)', 'kpg-elementor-widgets' ),
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

		$this->add_control(
			'filter_by_author',
			[
				'label' => esc_html__( 'Filter by Author Automatically', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => esc_html__( 'Automatically filter posts by author when on author archive pages (/author/name/)', 'kpg-elementor-widgets' ),
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

		// Filter by author if on author archive page and setting is enabled
		if ( $settings['filter_by_author'] === 'yes' ) {
			$author_id = null;
			
			// Try multiple methods to detect author
			if ( is_author() ) {
				$queried_object = get_queried_object();
				if ( $queried_object && isset( $queried_object->ID ) ) {
					$author_id = $queried_object->ID;
				} else {
					$author_id = get_queried_object_id();
				}
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
				if ( preg_match( '#/author/([^/]+)/?#', $request_uri, $matches ) ) {
					$author_slug = $matches[1];
					$author = get_user_by( 'slug', $author_slug );
					if ( $author ) {
						$author_id = $author->ID;
					} else {
						// Try as numeric ID
						$author_id = intval( $author_slug );
						if ( $author_id && ! get_user_by( 'ID', $author_id ) ) {
							$author_id = null;
						}
					}
				}
			}
			
			if ( $author_id ) {
				$args['author'] = $author_id;
			}
		}

		$query = new WP_Query( $args );
		?>
		
		<div class="kpg-blog-mobile-container">
			<?php if ( $query->have_posts() ) : ?>
				<div class="kpg-blog-mobile-list">
					<?php 
					$index = 0;
					$large_interval = intval( $settings['large_post_interval'] );
					
					while ( $query->have_posts() ) : 
						$query->the_post();
						$index++;
						
						// Separator before each post (except first)
						if ( $index > 1 ) {
							echo '<div class="kpg-post-separator"></div>';
						}
						
						// Determine if large format
						$is_large = ( $large_interval > 0 && $index % $large_interval === 0 );
						
						if ( $is_large ) {
							$this->render_large_post();
						} else {
							$this->render_regular_post();
						}
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			<?php endif; ?>
			
			<?php if ( $settings['show_pagination'] === 'yes' && $query->max_num_pages > 1 ) : 
				$this->render_pagination( $paged, $query->max_num_pages );
			endif; ?>
		</div>
		<?php
	}

	protected function render_regular_post() {
		?>
		<div class="kpg-post-mobile-item">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="kpg-post-mobile-image">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="kpg-post-mobile-image">
					<a href="<?php the_permalink(); ?>">
						<div class="kpg-post-mobile-image-placeholder"></div>
					</a>
				</div>
			<?php endif; ?>
			
			<div class="kpg-post-mobile-content">
				<h3 class="kpg-post-mobile-title">
					<a href="<?php the_permalink(); ?>">
						<?php the_title(); ?>
					</a>
				</h3>
				<div class="kpg-post-mobile-meta">
					<span class="kpg-post-mobile-author-date">
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
			</div>
		</div>
		<?php
	}

	protected function render_large_post() {
		?>
		<div class="kpg-post-mobile-large">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="kpg-post-mobile-large-image">
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="kpg-post-mobile-large-image">
					<a href="<?php the_permalink(); ?>">
						<div class="kpg-post-mobile-large-image-placeholder"></div>
					</a>
				</div>
			<?php endif; ?>
			
			<h2 class="kpg-post-mobile-large-title">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			
			<div class="kpg-post-mobile-large-excerpt-wrapper">
				<div class="kpg-post-mobile-large-excerpt-inner">
					<span class="kpg-post-mobile-large-excerpt">
						<?php echo esc_html( wp_trim_words( get_the_excerpt(), 50 ) ); ?>
					</span>
				</div>
			</div>
			
			<div class="kpg-post-mobile-large-meta">
				<div class="kpg-post-mobile-large-avatar">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
				</div>
				<span class="kpg-post-mobile-large-author-date">
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
		<div class="kpg-blog-mobile-container">
			<div class="kpg-blog-mobile-list">
				<div class="kpg-post-mobile-item">
					<div class="kpg-post-mobile-image" style="background: #e3ebec;"></div>
					<div class="kpg-post-mobile-content">
						<h3 class="kpg-post-mobile-title"><a href="#">Post Title</a></h3>
						<div class="kpg-post-mobile-meta">
							<span>Author • 23/12/25</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

