<?php
/**
 * Structured data.
 *
 * Everything emitted here is drawn from facts the client supplied:
 * the legal name, the founding year, the registered address, the
 * founder and a pan-India service area. There is deliberately NO
 * aggregateRating, no review count, no employee count, no turnover,
 * no certification and no opening hours, because none of those were
 * provided.
 *
 * Telephone and email are emitted only once they are configured. False
 * structured data is worse than absent structured data: it is a
 * manual-action risk and it misleads buyers.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'rc_output_schema', 5 );
/**
 * Print Organization and LocalBusiness JSON-LD on the front page, and
 * a WebPage node elsewhere.
 */
function rc_output_schema(): void {

	if ( rc_seo_plugin_active() ) {
		return;
	}

	$graph = array();

	if ( is_front_page() ) {
		$graph[] = rc_schema_website();
		$graph[] = rc_schema_organization();
		$graph[] = rc_schema_local_business();
	} else {
		// Inner pages reference the organisation by @id; a light copy keeps
		// the reference resolvable when a page is read on its own.
		$graph[] = rc_schema_organization();
	}

	$graph[] = rc_schema_webpage();

	if ( ! is_front_page() ) {
		$graph[] = rc_schema_breadcrumbs();
	}

	$faq = rc_schema_faq();
	if ( $faq ) {
		$graph[] = $faq;
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => array_values( array_filter( $graph ) ),
	);

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}

/**
 * The postal address node, shared by both business types.
 *
 * @return array<string, string>
 */
function rc_schema_address(): array {

	$address = rc_address();

	return array(
		'@type'           => 'PostalAddress',
		'streetAddress'   => $address['street'],
		'addressLocality' => $address['locality'],
		'postalCode'      => $address['postcode'],
		'addressCountry'  => $address['country'],
	);
}

/**
 * Organization node.
 *
 * @return array<string, mixed>
 */
function rc_schema_organization(): array {

	$node = array(
		'@type'        => 'Organization',
		'@id'           => home_url( '/#organization' ),
		'name'          => get_bloginfo( 'name' ),
		'alternateName' => rc_brand_alternate_names(),
		'url'           => home_url( '/' ),
		'foundingDate' => (string) RC_FOUNDED,
		'founder'      => array(
			'@type' => 'Person',
			'name'  => 'Indu Bhatia',
		),
		'address'      => rc_schema_address(),
		'areaServed'   => rc_schema_area_served(),
		'description'  => rc_meta_descriptions()['_front'],
	);

	// The square mark, not the lockup: search engines crop logos to a
	// square, and the lockup's "40+ years" line would disagree with the
	// founding date above.
	$logo = rc_image_sources( 'logo-square-512' );

	if ( $logo ) {
		$node['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => (string) reset( $logo ),
		);
	}

	return rc_schema_add_channels( $node );
}

/**
 * LocalBusiness node.
 *
 * @return array<string, mixed>
 */
function rc_schema_local_business(): array {

	$node = array(
		'@type'              => array( 'LocalBusiness', 'RecyclingCenter' ),
		'@id'                => home_url( '/#localbusiness' ),
		'name'               => get_bloginfo( 'name' ),
		'alternateName'      => rc_brand_alternate_names(),
		'url'                => home_url( '/' ),
		'foundingDate'       => (string) RC_FOUNDED,
		'address'            => rc_schema_address(),
		'areaServed'         => rc_schema_area_served(),
		'parentOrganization' => array( '@id' => home_url( '/#organization' ) ),
		'knowsAbout'         => array( 'Glass cullet', 'Glass recycling', 'Flint glass cullet', 'Amber glass cullet', 'Green glass cullet', 'Factory cullet', 'Post-consumer glass cullet' ),
		'hasOfferCatalog'    => array(
			'@type'           => 'OfferCatalog',
			'name'            => __( 'Glass cullet', 'rian-cullet' ),
			'itemListElement' => array_map(
				static function ( string $name ): array {
					return array(
						'@type'       => 'Offer',
						'itemOffered' => array(
							'@type' => 'Product',
							'name'  => $name,
						),
					);
				},
				array( 'Factory glass cullet', 'Foreign (post-consumer) glass cullet', 'Flint (clear) glass cullet', 'Amber glass cullet', 'Green glass cullet' )
			),
		),
	);

	$image = rc_image_sources( 'og-default' );
	if ( $image ) {
		$node['image'] = (string) reset( $image );
	}

	return rc_schema_add_channels( $node );
}

/**
 * The area served: all of India, as the client states.
 *
 * @return array<string, string>
 */
function rc_schema_area_served(): array {
	return array(
		'@type' => 'Country',
		'name'  => 'India',
	);
}

/**
 * Attach telephone and email only when they have been configured.
 *
 * @param array<string, mixed> $node Schema node.
 * @return array<string, mixed>
 */
function rc_schema_add_channels( array $node ): array {

	$channels = rc_contact_channels();

	if ( ! empty( $channels['phone'] ) ) {
		$node['telephone'] = $channels['phone'];
	}

	if ( ! empty( $channels['email'] ) ) {
		$node['email'] = $channels['email'];
	}

	return $node;
}

/**
 * WebPage node for the current view.
 *
 * @return array<string, mixed>
 */
function rc_schema_webpage(): array {

	$url = is_singular() ? (string) get_permalink() : home_url( add_query_arg( array() ) );

	return array(
		'@type'       => 'WebPage',
		'@id'         => $url . '#webpage',
		'url'         => $url,
		'name'        => html_entity_decode( wp_get_document_title(), ENT_QUOTES, 'UTF-8' ),
		'description' => rc_meta_description(),
		'isPartOf'    => array( '@id' => home_url( '/#website' ) ),
		'about'       => array( '@id' => home_url( '/#organization' ) ),
		'inLanguage'  => 'en-IN',
	);
}

/**
 * WebSite node. Google reads this to choose the site name it shows in
 * results, which is why it matters that it says "Rian Cullet" rather
 * than the bare domain.
 *
 * @return array<string, mixed>
 */
function rc_schema_website(): array {
	return array(
		'@type'         => 'WebSite',
		'@id'           => home_url( '/#website' ),
		'url'           => home_url( '/' ),
		'name'          => get_bloginfo( 'name' ),
		'alternateName' => rc_brand_alternate_names(),
		'publisher'     => array( '@id' => home_url( '/#organization' ) ),
		'inLanguage'    => 'en-IN',
	);
}

/**
 * Other names people search for the company by. The logo lockup reads
 * "RIAN CULLETS" and the domain is riancullets.com.
 *
 * @return string[]
 */
function rc_brand_alternate_names(): array {
	return array_values( array_diff( array( 'Rian Cullets', 'Rian Cullet', 'riancullets.com' ), array( get_bloginfo( 'name' ) ) ) );
}

/**
 * BreadcrumbList node matching the visible trail.
 *
 * @return array<string, mixed>
 */
function rc_schema_breadcrumbs(): array {

	$items = array();

	foreach ( rc_breadcrumbs() as $i => $crumb ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb['label'],
			'item'     => $crumb['url'] ? $crumb['url'] : ( is_singular() ? (string) get_permalink() : home_url( add_query_arg( array() ) ) ),
		);
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => ( is_singular() ? (string) get_permalink() : home_url( '/' ) ) . '#breadcrumb',
		'itemListElement' => $items,
	);
}

/**
 * FAQPage node for landing pages that carry questions.
 *
 * @return array<string, mixed>|null
 */
function rc_schema_faq(): ?array {

	$page = function_exists( 'rc_current_seo_page' ) ? rc_current_seo_page() : null;

	if ( ! $page || empty( $page['faqs'] ) ) {
		return null;
	}

	return array(
		'@type'      => 'FAQPage',
		'@id'        => (string) get_permalink() . '#faq',
		'mainEntity' => array_map(
			static function ( array $faq ): array {
				return array(
					'@type'          => 'Question',
					'name'           => $faq[0],
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => $faq[1],
					),
				);
			},
			$page['faqs']
		),
	);
}
