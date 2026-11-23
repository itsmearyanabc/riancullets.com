<?php
/**
 * SEO output: meta description, canonical, Open Graph, robots.
 *
 * The theme stands down entirely if Yoast or Rank Math is active, so a
 * later plugin install cannot produce two competing sets of tags.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Whether a dedicated SEO plugin is handling metadata.
 *
 * @return bool
 */
function rc_seo_plugin_active(): bool {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'All_in_One_SEO_Pack' );
}

/**
 * Default meta descriptions, keyed by page slug.
 *
 * One primary intent per page keeps the six pages from competing with
 * each other for the same query.
 *
 * @return array<string, string>
 */
function rc_meta_descriptions(): array {
	return array(
		/* translators: %d: founding year */
		'_front'         => sprintf( __( 'Rian Cullet is a generational glass cullet business from Delhi, established in %d. We collect, sort and process recycled glass cullet, serving customers pan-India.', 'rian-cullet' ), RC_FOUNDED ),
		/* translators: %d: founding year */
		'about'          => sprintf( __( 'Established in Delhi in %d and founded by Ms. Indu Bhatia, Rian Cullet collects, sorts and processes glass cullet for reuse in glass manufacturing.', 'rian-cullet' ), RC_FOUNDED ),
		'glass-cullet'   => __( 'What glass cullet is: factory cullet, foreign post-consumer cullet and flint (white) cullet, and how container and flat glass are sorted by colour for reuse.', 'rian-cullet' ),
		'process'        => __( 'How Rian Cullet turns recovered glass into usable cullet: collection, sorting by type and colour, processing, and supply to glass manufacturers.', 'rian-cullet' ),
		'sustainability' => __( 'Recovered glass returned to the manufacturing cycle. How recycled cullet supports lower melting requirements and a more circular glass industry.', 'rian-cullet' ),
		'contact'        => __( 'Contact Rian Cullet in Delhi to discuss glass cullet requirements. Enquire about factory, foreign or flint cullet. Serving customers pan-India.', 'rian-cullet' ),
	);
}

/**
 * Resolve the meta description for the current view.
 *
 * @return string
 */
function rc_meta_description(): string {

	$map = rc_meta_descriptions();

	if ( is_front_page() ) {
		$description = $map['_front'];
	} elseif ( is_page() ) {
		$slug        = (string) get_post_field( 'post_name', get_queried_object_id() );
		$description = $map[ $slug ] ?? '';

		if ( '' === $description ) {
			$description = (string) get_the_excerpt();
		}
	} else {
		$description = get_bloginfo( 'description' );
	}

	/**
	 * Filters the meta description.
	 *
	 * @param string $description Description text.
	 */
	$description = (string) apply_filters( 'rc_meta_description', $description );

	return trim( wp_strip_all_tags( $description ) );
}

add_filter( 'document_title_separator', 'rc_title_separator' );
/**
 * Use a pipe between page and site name: "About | Rian Cullet".
 *
 * @param string $separator Default separator.
 * @return string
 */
function rc_title_separator( string $separator ): string {
	return rc_seo_plugin_active() ? $separator : '|';
}

add_filter( 'document_title_parts', 'rc_title_parts' );
/**
 * Lead the homepage title with the primary search intent rather than
 * the bare company name, which is all WordPress gives it by default.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function rc_title_parts( array $parts ): array {

	if ( rc_seo_plugin_active() ) {
		return $parts;
	}

	if ( ! is_front_page() ) {
		// Keyword-led titles for the core pages, instead of a bare
		// "About | Rian Cullet". Kept under ~60 characters with the brand.
		$titles = array(
			'about'          => __( 'About Us: Delhi Glass Cullet Company Since 1996', 'rian-cullet' ),
			'glass-cullet'   => __( 'What Is Glass Cullet? Factory, Foreign & Flint', 'rian-cullet' ),
			'process'        => __( 'Glass Cullet Collection, Sorting & Processing', 'rian-cullet' ),
			'sustainability' => __( 'Glass Recycling & Sustainability', 'rian-cullet' ),
			'contact'        => __( 'Contact Us: Glass Cullet Enquiries, Delhi', 'rian-cullet' ),
		);

		$slug = is_page() ? (string) get_post_field( 'post_name', get_queried_object_id() ) : '';

		if ( isset( $titles[ $slug ] ) ) {
			$parts['title'] = $titles[ $slug ];
		}

		return $parts;
	}

	return array(
		'title' => __( 'Glass Cullet Supplier & Recycling Company in Delhi', 'rian-cullet' ),
		'site'  => get_bloginfo( 'name' ),
	);
}

add_action(
	'wp',
	static function (): void {
		// The theme prints its own canonical in rc_output_meta(); without
		// this, singular pages carried two canonical tags.
		if ( ! rc_seo_plugin_active() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

add_action( 'wp_head', 'rc_output_meta', 2 );
/**
 * Print description, canonical, robots and Open Graph tags.
 */
function rc_output_meta(): void {

	if ( rc_seo_plugin_active() ) {
		return;
	}

	$description = rc_meta_description();
	$canonical   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$title       = wp_get_document_title();

	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
	}

	// The privacy policy has no content yet, so keep it out of the index
	// until the client supplies the real text.
	if ( $og_alt = rc_og_image_alt() ) { // phpcs:ignore
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $og_alt ) );
	}

	printf( '<meta property="og:type" content="%s">' . "\n", 'website' );
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", 'en_US' === get_locale() ? 'en_IN' : esc_attr( get_locale() ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );

	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $canonical ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $canonical ) );
	}

	$og_image = rc_og_image();

	if ( $og_image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $og_image ) );
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	} else {
		echo '<meta name="twitter:card" content="summary">' . "\n";
	}

	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );

	if ( $description ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
	}
}

/**
 * The sharing image: the page thumbnail, else the theme default.
 *
 * Returns an empty string rather than a broken URL when neither exists,
 * so no tag is printed at all.
 *
 * @return string
 */
function rc_og_image(): string {

	if ( is_singular() && has_post_thumbnail() ) {
		$thumb = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
		if ( $thumb ) {
			return (string) $thumb;
		}
	}

	$default = rc_image_sources( 'og-default' );

	return $default ? (string) reset( $default ) : '';
}

/**
 * Alt text for the default sharing image.
 *
 * @return string
 */
function rc_og_image_alt(): string {
	return rc_og_image() ? __( 'Sorted glass cullet in clear, amber and green: Rian Cullet, Delhi.', 'rian-cullet' ) : '';
}

/* --------------------------------------------------------------------------
   Index hygiene
   -------------------------------------------------------------------------- */

add_filter( 'language_attributes', 'rc_language_attributes' );
/**
 * Declare Indian English. The audience is in India and WordPress defaults
 * to en-US; the lang attribute is a (small) regional relevance signal.
 *
 * @param string $output Attributes.
 * @return string
 */
function rc_language_attributes( string $output ): string {
	return str_replace( 'lang="en-US"', 'lang="en-IN"', $output );
}

add_filter( 'wp_sitemaps_add_provider', 'rc_sitemap_providers', 10, 2 );
/**
 * Drop the users sitemap: it publishes the admin username and has no
 * search value for a business site.
 *
 * @param WP_Sitemaps_Provider|false $provider Provider.
 * @param string                     $name     Provider name.
 * @return WP_Sitemaps_Provider|false
 */
function rc_sitemap_providers( $provider, string $name ) {
	return 'users' === $name ? false : $provider;
}

add_filter( 'wp_sitemaps_taxonomies', '__return_empty_array' );

add_filter( 'wp_sitemaps_post_types', 'rc_sitemap_post_types' );
/**
 * Pages only, unless the client starts publishing posts.
 *
 * @param array<string, WP_Post_Type> $types Post types.
 * @return array<string, WP_Post_Type>
 */
function rc_sitemap_post_types( array $types ): array {
	$counts = wp_count_posts( 'post' );
	if ( empty( $counts->publish ) ) {
		unset( $types['post'] );
	}
	unset( $types['attachment'], $types['rc_enquiry'] );
	return $types;
}

add_filter( 'wp_sitemaps_posts_query_args', 'rc_sitemap_exclude' );
/**
 * Keep the empty privacy page out of the sitemap while it is noindex.
 *
 * @param array<string, mixed> $args Query args.
 * @return array<string, mixed>
 */
function rc_sitemap_exclude( array $args ): array {
	$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( $privacy && '' === trim( (string) get_post_field( 'post_content', $privacy ) ) ) {
		$args['post__not_in'] = array_merge( (array) ( $args['post__not_in'] ?? array() ), array( $privacy ) );
	}
	return $args;
}

add_filter( 'wp_sitemaps_posts_entry', 'rc_sitemap_lastmod', 10, 2 );
/**
 * Add lastmod. Page content lives in the theme, so the theme's own
 * modification time counts as a change too.
 *
 * @param array<string, string> $entry Entry.
 * @param WP_Post               $post  Post.
 * @return array<string, string>
 */
function rc_sitemap_lastmod( array $entry, $post ): array {
	$post_time  = strtotime( (string) $post->post_modified_gmt );
	$theme_time = (int) @filemtime( RC_DIR . '/functions.php' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	$entry['lastmod'] = gmdate( 'c', max( (int) $post_time, $theme_time ) );
	return $entry;
}

add_action( 'template_redirect', 'rc_redirect_author_archives', 1 );
/**
 * Author archives duplicate the homepage and expose usernames
 * (/?author=1 enumeration). Send them home.
 */
function rc_redirect_author_archives(): void {
	if ( is_author() || ( isset( $_GET['author'] ) && ! is_admin() ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}

add_filter( 'rest_endpoints', 'rc_hide_user_endpoints' );
/**
 * Hide the users REST endpoints from anonymous visitors.
 *
 * @param array<string, mixed> $endpoints Endpoints.
 * @return array<string, mixed>
 */
function rc_hide_user_endpoints( array $endpoints ): array {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( 0 === strpos( (string) $route, '/wp/v2/users' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}

add_filter( 'feed_links_show_comments_feed', '__return_false' );

add_filter( 'wp_robots', 'rc_robots_preview' );
/**
 * Allow large image previews and full snippets in search results.
 *
 * @param array<string, bool|string> $robots Directives.
 * @return array<string, bool|string>
 */
function rc_robots_preview( array $robots ): array {

	// Thin or duplicate views stay out of the index. So does the privacy
	// page until the client supplies its text.
	$empty_privacy = is_page( 'privacy-policy' ) && '' === trim( (string) get_post_field( 'post_content', get_queried_object_id() ) );

	if ( ! rc_seo_plugin_active() && ( $empty_privacy || is_search() || is_date() || is_attachment() || is_404() || is_category() || is_tag() ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	$robots['max-snippet']       = '-1';
	$robots['max-video-preview'] = '-1';
	$robots['max-image-preview'] = 'large';
	return $robots;
}

/* --------------------------------------------------------------------------
   robots.txt and llms.txt
   -------------------------------------------------------------------------- */

add_filter( 'robots_txt', 'rc_robots_txt', 20, 2 );
/**
 * A short, explicit robots.txt.
 *
 * @param string $output Default output.
 * @param bool   $public Whether the site is public.
 * @return string
 */
function rc_robots_txt( string $output, $public ): string {
	if ( ! $public ) {
		return $output;
	}
	$lines = array(
		'User-agent: *',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
		'Disallow: /?s=',
		'Disallow: /search/',
		'',
		'Sitemap: ' . home_url( '/wp-sitemap.xml' ),
		'',
	);
	return implode( "\n", $lines );
}

add_action( 'init', 'rc_llms_rewrite' );
/**
 * Serve /llms.txt: a plain-text summary that AI search tools read. The
 * live site already advertised one (via a Hostinger plugin) that 404'd.
 */
function rc_llms_rewrite(): void {
	add_rewrite_rule( '^llms\.txt$', 'index.php?rc_llms=1', 'top' );
}

add_filter( 'query_vars', static function ( array $vars ): array {
	$vars[] = 'rc_llms';
	return $vars;
} );

add_action( 'template_redirect', 'rc_llms_output', 0 );
/**
 * Print llms.txt.
 */
function rc_llms_output(): void {

	if ( ! get_query_var( 'rc_llms' ) ) {
		return;
	}

	$address = rc_address();
	$name    = get_bloginfo( 'name' );

	$out  = '# ' . $name . "\n\n";
	$out .= '> ' . rc_meta_descriptions()['_front'] . "\n\n";
	$out .= sprintf(
		"- Established: %d, Delhi, India (founder: Ms. Indu Bhatia)\n- Serves: pan-India; %d+ companies in %d+ states\n- Address: %s, %s %s, India\n\n",
		RC_FOUNDED,
		RC_COMPANIES_SERVED,
		RC_STATES_SERVED,
		$address['street'],
		$address['locality'],
		$address['postcode']
	);

	$out .= "## Pages\n\n";

	$core = array(
		'/'                => __( 'Home', 'rian-cullet' ),
		'/about/'          => __( 'About', 'rian-cullet' ),
		'/glass-cullet/'   => __( 'Glass cullet: factory, foreign and flint cullet', 'rian-cullet' ),
		'/process/'        => __( 'Process: collection, sorting, processing, supply', 'rian-cullet' ),
		'/sustainability/' => __( 'Sustainability', 'rian-cullet' ),
		'/contact/'        => __( 'Contact and enquiries', 'rian-cullet' ),
	);

	foreach ( $core as $path => $label ) {
		$out .= '- [' . $label . '](' . home_url( $path ) . ")\n";
	}

	if ( function_exists( 'rc_seo_pages' ) ) {
		foreach ( rc_seo_pages() as $slug => $page ) {
			$out .= '- [' . $page['nav'] . '](' . rc_seo_page_url( $slug ) . '): ' . $page['meta'] . "\n";
		}
	}

	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput -- plain text.
	exit;
}
