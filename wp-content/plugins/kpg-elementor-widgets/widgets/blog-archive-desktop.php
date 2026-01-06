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

		$this->add_control(
			'filter_by_author',
			[
				'label' => esc_html__( 'Filter by Author Automatically', 'kpg-elementor-widgets' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => esc_html__( 'Automatically filter posts by author when on author archive pages (/autor/name/)', 'kpg-elementor-widgets' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		$paged = max( 1, get_query_var( 'paged' ) );
		$sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'newest';
		$order = ( $sort === 'oldest' ) ? 'ASC' : 'DESC';
		
		// Get search query parameter
		$search_query = '';
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$search_query = sanitize_text_field( $_GET['s'] );
		} elseif ( get_query_var( 's' ) ) {
			$search_query = get_query_var( 's' );
		}

		$args = [
			'post_type' => 'post',
			'posts_per_page' => intval( $settings['posts_per_page'] ),
			'paged' => $paged,
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
		];
		
		// Add search query if present
		if ( ! empty( $search_query ) ) {
			$args['s'] = $search_query;
			// When searching, sort by relevance (WordPress searches in title, content, excerpt)
			$args['orderby'] = 'relevance';
			$args['order'] = 'DESC'; // Relevance is always DESC
		} else {
			// When not searching, sort by date
			$args['orderby'] = 'date';
			$args['order'] = $order;
		}

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
			
			// Fallback: check URL directly (check both /autor/ and /author/ for backward compatibility)
			if ( ! $author_id ) {
				$request_uri = $_SERVER['REQUEST_URI'] ?? '';
				if ( preg_match( '#/(?:autor|author)/([^/]+)/?#', $request_uri, $matches ) ) {
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
		
		<div class="kpg-blog-desktop-container">
			<?php if ( $query->have_posts() ) : ?>
				<div class="kpg-blog-desktop-grid">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<article class="kpg-blog-desktop-item">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="kpg-blog-desktop-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'medium', [ 'style' => 'width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;' ] ); ?>
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
									<?php 
									$author_id = get_the_author_meta( 'ID' );
									$avatar_url = kpg_get_author_avatar_url( $author_id, 32 );
									$first = get_the_author_meta( 'first_name' );
									$last = get_the_author_meta( 'last_name' );
									$name = trim( $first . ' ' . $last );
									if ( empty( $name ) ) {
										$name = get_the_author();
									}
									?>
									<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" width="32" height="32" style="border-radius: 50%;" />
								</div>
								<span class="kpg-blog-desktop-author-date">
									<?php 
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
		
		// Get blog base URL
		$posts_page_id = get_option( 'page_for_posts' );
		if ( $posts_page_id ) {
			$blog_base_url = rtrim( get_permalink( $posts_page_id ), '/' );
		} else {
			$blog_base_url = rtrim( home_url( '/blog' ), '/' );
		}
		
		// Preserve search query in pagination
		$search_query = '';
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$search_query = sanitize_text_field( $_GET['s'] );
		} elseif ( get_query_var( 's' ) ) {
			$search_query = get_query_var( 's' );
		}
		?>
		<div class="kpg-blog-separator"></div>
		<div class="kpg-blog-pagination" 
			 data-blog-base-url="<?php echo esc_attr( $blog_base_url ); ?>"
			 data-search-query="<?php echo esc_attr( $search_query ); ?>">
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

