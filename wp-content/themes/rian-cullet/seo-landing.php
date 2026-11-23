<?php
/**
 * Search landing page.
 *
 * Shared layout for every page defined in inc/seo-pages.php: breadcrumb
 * and heading, an introduction beside a photograph, the content sections,
 * the company facts, questions and answers, related pages and the call to
 * action. Questions are real <details> elements, so the answers are in
 * the HTML for crawlers and work without JavaScript.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$rc_page  = rc_current_seo_page();
$rc_pages = rc_seo_pages();

get_header();

get_template_part(
	'template-parts/sections/page-hero',
	null,
	array(
		'eyebrow' => (string) $rc_page['eyebrow'],
		'title'   => (string) $rc_page['h1'],
		'lede'    => (string) $rc_page['lede'],
		'crumbs'  => rc_breadcrumbs(),
	)
);

if ( ! empty( $rc_page['intro'] ) ) :
	?>
	<section class="rc-section rc-section--flush-top rc-surface-paper">
		<div class="rc-container rc-container--wide">
			<div class="rc-split rc-split--7-5">
				<div class="rc-reveal">
					<div class="rc-stack">
						<?php foreach ( $rc_page['intro'] as $rc_para ) : ?>
							<p class="rc-body rc-landing__intro"><?php echo esc_html( $rc_para ); ?></p>
						<?php endforeach; ?>
					</div>
					<div class="rc-landing__actions">
						<?php
						rc_button(
							array(
								'label' => __( 'Send an enquiry', 'rian-cullet' ),
								'url'   => home_url( '/contact/#rc-enquiry' ),
							)
						);
						?>
					</div>
				</div>
				<?php if ( ! empty( $rc_page['image'] ) ) : ?>
					<div class="rc-reveal" style="--rc-i:1">
						<?php
						rc_figure(
							array(
								'name'     => (string) $rc_page['image'],
								'alt'      => (string) $rc_page['alt'],
								'ratio'    => (string) ( $rc_page['image_ratio'] ?? 'rc-ratio-portrait' ),
								'width'    => (int) ( $rc_page['image_w'] ?? 1200 ),
								'height'   => (int) ( $rc_page['image_h'] ?? 1500 ),
								'sizes'    => '(min-width: 62rem) 40vw, 100vw',
								'priority' => true,
							)
						);
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endif;

foreach ( (array) $rc_page['sections'] as $rc_i => $rc_section ) :
	$rc_id = 'rc-s-' . (int) $rc_i;
	?>
	<section class="rc-section rc-section--tight rc-surface-paper rc-landing__section" aria-labelledby="<?php echo esc_attr( $rc_id ); ?>">
		<div class="rc-container rc-container--wide">
			<div class="rc-split rc-split--5-7 rc-landing__row">
				<div class="rc-reveal">
					<span class="rc-index"><?php echo esc_html( sprintf( '%02d', $rc_i + 1 ) ); ?></span>
					<h2 class="rc-display-3" id="<?php echo esc_attr( $rc_id ); ?>"><?php echo esc_html( $rc_section['h2'] ); ?></h2>
				</div>
				<div class="rc-reveal" style="--rc-i:1">
					<?php foreach ( (array) ( $rc_section['body'] ?? array() ) as $rc_para ) : ?>
						<p class="rc-body"><?php echo esc_html( $rc_para ); ?></p>
					<?php endforeach; ?>

					<?php if ( ! empty( $rc_section['list'] ) ) : ?>
						<?php $rc_tag = ! empty( $rc_section['ordered'] ) ? 'ol' : 'ul'; ?>
						<<?php echo $rc_tag; // phpcs:ignore ?> class="rc-landing__list<?php echo 'ol' === $rc_tag ? ' rc-landing__list--steps' : ''; ?>">
							<?php foreach ( $rc_section['list'] as $rc_item ) : ?>
								<li><?php echo esc_html( $rc_item ); ?></li>
							<?php endforeach; ?>
						</<?php echo $rc_tag; // phpcs:ignore ?>>
					<?php endif; ?>

					<?php foreach ( (array) ( $rc_section['body_after'] ?? array() ) as $rc_para ) : ?>
						<p class="rc-body rc-landing__after"><?php echo esc_html( $rc_para ); ?></p>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
endforeach;

get_template_part( 'template-parts/sections/stats' );

if ( ! empty( $rc_page['faqs'] ) ) :
	?>
	<section class="rc-section rc-surface-paper" aria-labelledby="rc-faq-title">
		<div class="rc-container rc-container--wide">
			<div class="rc-split rc-split--5-7">
				<div class="rc-section-head rc-reveal">
					<?php rc_eyebrow( __( 'Questions', 'rian-cullet' ) ); ?>
					<h2 class="rc-display-2" id="rc-faq-title"><?php esc_html_e( 'Frequently asked', 'rian-cullet' ); ?></h2>
				</div>
				<div class="rc-faq rc-reveal" style="--rc-i:1">
					<?php foreach ( $rc_page['faqs'] as $rc_n => $rc_faq ) : ?>
						<details class="rc-faq__item"<?php echo 0 === $rc_n ? ' open' : ''; ?>>
							<summary class="rc-faq__q"><?php echo esc_html( $rc_faq[0] ); ?></summary>
							<div class="rc-faq__a"><p class="rc-body"><?php echo esc_html( $rc_faq[1] ); ?></p></div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
endif;

$rc_related = array_values(
	array_filter(
		(array) ( $rc_page['related'] ?? array() ),
		static function ( $slug ) use ( $rc_pages ) {
			return isset( $rc_pages[ $slug ] );
		}
	)
);

if ( $rc_related ) :
	?>
	<section class="rc-section rc-section--tight rc-surface-paper" aria-labelledby="rc-related-title">
		<div class="rc-container rc-container--wide">
			<div class="rc-section-head rc-reveal">
				<?php rc_eyebrow( __( 'Related', 'rian-cullet' ) ); ?>
				<h2 class="rc-display-3" id="rc-related-title"><?php esc_html_e( 'Keep reading', 'rian-cullet' ); ?></h2>
			</div>
			<ul class="rc-related rc-cols rc-cols--<?php echo count( $rc_related ) >= 4 ? '4' : '3'; ?>">
				<?php foreach ( $rc_related as $rc_n => $rc_slug ) : ?>
					<li class="rc-reveal" style="--rc-i:<?php echo (int) $rc_n; ?>">
						<a class="rc-related__link" href="<?php echo esc_url( rc_seo_page_url( $rc_slug ) ); ?>">
							<span class="rc-related__title"><?php echo esc_html( $rc_pages[ $rc_slug ]['nav'] ); ?></span>
							<span class="rc-related__lede rc-small"><?php echo esc_html( $rc_pages[ $rc_slug ]['lede'] ); ?></span>
							<span class="rc-related__arrow" aria-hidden="true">&rarr;</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
endif;

get_template_part( 'template-parts/sections/cta' );

get_footer();
