<?php
/**
 * Inner page header.
 *
 * Deliberately not a full-viewport hero. Only the homepage earns that;
 * on inner pages it would delay the content the visitor came for.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

$rc_args = wp_parse_args(
	$args ?? array(),
	array(
		'eyebrow' => '',
		'title'   => get_the_title(),
		'lede'    => '',
		'crumbs'  => array(),
	)
);
?>
<section class="rc-section rc-section--tight rc-surface-paper rc-page-hero" style="padding-top:calc(var(--rc-header-h) + var(--rc-space-9))">
	<div class="rc-container rc-container--wide">
		<?php if ( $rc_args['crumbs'] ) : ?>
			<nav class="rc-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'rian-cullet' ); ?>">
				<ol>
					<?php foreach ( $rc_args['crumbs'] as $rc_crumb ) : ?>
						<li>
							<?php if ( ! empty( $rc_crumb['url'] ) ) : ?>
								<a href="<?php echo esc_url( $rc_crumb['url'] ); ?>"><?php echo esc_html( $rc_crumb['label'] ); ?></a>
							<?php else : ?>
								<span aria-current="page"><?php echo esc_html( $rc_crumb['label'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>
		<div class="rc-section-head rc-reveal">
			<?php
			if ( $rc_args['eyebrow'] ) {
				rc_eyebrow( $rc_args['eyebrow'] );
			}
			?>
			<h1 class="rc-display-1" style="font-size:var(--rc-size-h2)"><?php echo esc_html( $rc_args['title'] ); ?></h1>
			<?php if ( $rc_args['lede'] ) : ?>
				<p class="rc-lede"><?php echo esc_html( $rc_args['lede'] ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
