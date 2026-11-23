<?php
/**
 * Self-provisioning.
 *
 * The first live deployment never ran "Create pages and menus", so every
 * link in the navigation returned a 404. Relying on someone clicking an
 * admin notice is too fragile, so the theme now provisions itself: the
 * first request after a new theme version is installed creates any
 * missing pages, publishes empty drafts at the expected slugs, removes
 * WordPress's untouched sample content and flushes rewrite rules.
 *
 * It is idempotent and never overwrites edited content. It runs once per
 * RC_VERSION and is guarded by a short lock so concurrent requests cannot
 * create duplicates.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'rc_provision_reset' );
/**
 * Force a provisioning pass when the theme is (re)activated.
 */
function rc_provision_reset(): void {
	delete_option( 'rc_provisioned_version' );
}

add_action( 'init', 'rc_maybe_provision', 99 );
/**
 * Run the provisioning pass once per theme version.
 */
function rc_maybe_provision(): void {

	if ( RC_VERSION === get_option( 'rc_provisioned_version' ) ) {
		return;
	}

	if ( wp_installing() || ( defined( 'WP_CLI' ) && WP_CLI && ! defined( 'RC_PROVISION_CLI' ) ) ) {
		return;
	}

	// add_option() fails if the row exists, which makes it a cheap lock.
	$lock = get_option( 'rc_provision_lock' );
	if ( $lock && ( time() - (int) $lock ) < 60 ) {
		return;
	}
	if ( false === $lock ) {
		if ( ! add_option( 'rc_provision_lock', (string) time(), '', false ) ) {
			return;
		}
	} else {
		update_option( 'rc_provision_lock', (string) time(), false );
	}

	require_once RC_DIR . '/inc/starter-content.php';

	rc_create_starter_content();
	rc_fix_site_identity();
	rc_remove_sample_content();

	flush_rewrite_rules( false );

	update_option( 'rc_provisioned_version', RC_VERSION );
	delete_option( 'rc_provision_lock' );

	rc_purge_page_cache();
}

/**
 * The live site title was left as the domain name ("riancullets.com"),
 * which leaked into every <title>, the wordmark and the structured data.
 * Replace it only when it is still a bare domain; a real name typed in
 * Settings > General is never touched.
 */
function rc_fix_site_identity(): void {

	$name = (string) get_option( 'blogname' );

	if ( '' === $name || rc_looks_like_domain( $name ) ) {
		update_option( 'blogname', RC_BRAND_NAME );
	}

	$tagline = (string) get_option( 'blogdescription' );

	if ( '' === $tagline || 'Just another WordPress site' === $tagline ) {
		update_option( 'blogdescription', __( 'Glass cullet supplier and recycler, Delhi. Serving pan-India since 1996.', 'rian-cullet' ) );
	}
}

/**
 * Whether a string is a bare host name such as "riancullets.com".
 *
 * @param string $value Value to test.
 * @return bool
 */
function rc_looks_like_domain( string $value ): bool {
	return (bool) preg_match( '/^(https?:\/\/)?(www\.)?[a-z0-9-]+(\.[a-z0-9-]+)+\/?$/i', trim( $value ) );
}

add_filter( 'option_blogname', 'rc_filter_blogname' );
/**
 * Belt and braces: even before provisioning has run, never print a bare
 * domain as the company name.
 *
 * @param mixed $value Stored option.
 * @return mixed
 */
function rc_filter_blogname( $value ) {
	if ( is_string( $value ) && ( '' === $value || rc_looks_like_domain( $value ) ) && ! is_admin() ) {
		return RC_BRAND_NAME;
	}
	return $value;
}

/**
 * Trash WordPress's "Hello world!" post and "Sample Page" if nobody has
 * edited them. They were live and indexed on the first deployment.
 */
function rc_remove_sample_content(): void {

	$samples = array(
		array( 'hello-world', 'post' ),
		array( 'sample-page', 'page' ),
	);

	foreach ( $samples as [ $slug, $type ] ) {
		$posts = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => $type,
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'posts_per_page' => 1,
			)
		);

		foreach ( $posts as $post ) {
			// Untouched sample content is modified within a minute of creation.
			$age = abs( strtotime( $post->post_modified_gmt ) - strtotime( $post->post_date_gmt ) );
			if ( $age < 120 ) {
				wp_trash_post( $post->ID );
			}
		}
	}
}

/**
 * Purge any page cache so visitors see the new markup immediately.
 * LiteSpeed Cache is what Hostinger runs.
 */
function rc_purge_page_cache(): void {
	do_action( 'litespeed_purge_all' );

	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}
}
