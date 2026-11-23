<?php
/**
 * Search landing pages.
 *
 * Each page targets one buyer or seller intent that the six core pages do
 * not answer on their own: a supplier in Delhi, a buyer for glass scrap,
 * each cullet colour, glass recycling in India, and the common questions.
 * One intent per URL keeps the pages from competing with each other.
 *
 * The copy is written from the facts the client supplied (Delhi, 1996,
 * pan-India, 35+ companies, 22+ states, factory / foreign / flint cullet,
 * sorting by colour) plus general, well-established glass industry
 * knowledge. It makes no claim about tonnage, certification, price,
 * specification or named customers, because none was supplied.
 *
 * Every page is rendered by seo-landing.php from the data below, so the
 * copy lives in one place and the pages share one layout.
 *
 * @package RianCullet
 */

declare( strict_types = 1 );

defined( 'ABSPATH' ) || exit;

/**
 * Page definitions, keyed by slug.
 *
 * @return array<string, array<string, mixed>>
 */
function rc_seo_pages(): array {

	$years  = (int) gmdate( 'Y' ) - RC_FOUNDED;
	$states = RC_STATES_SERVED;
	$cos    = RC_COMPANIES_SERVED;

	$pages = array(

		/* ------------------------------------------------------------------ */
		'glass-cullet-supplier-delhi' => array(
			'nav'     => __( 'Glass Cullet Supplier in Delhi', 'rian-cullet' ),
			'title'   => __( 'Glass Cullet Supplier in Delhi NCR', 'rian-cullet' ),
			'meta'    => sprintf( __( 'Glass cullet supplier in Delhi since %d. Factory, foreign and flint cullet, sorted by colour and processed for glass manufacturers across India. Enquire today.', 'rian-cullet' ), RC_FOUNDED ),
			'eyebrow' => __( 'Supply / Delhi NCR', 'rian-cullet' ),
			'h1'      => __( 'Glass cullet supplier in Delhi NCR', 'rian-cullet' ),
			'lede'    => sprintf( __( 'Sorted, processed glass cullet from Tikri Kalan, Delhi, supplied to glass manufacturers across India since %d.', 'rian-cullet' ), RC_FOUNDED ),
			'image'   => 'factory-cullet',
			'alt'     => __( 'A yard of sorted glass cullet ready for dispatch to a glass manufacturer.', 'rian-cullet' ),
			'intro'   => array(
				sprintf( __( 'Rian Cullet has supplied glass cullet from Delhi for %1$d years. We collect waste glass, sort it by type and colour, process it into cullet a furnace can accept, and supply it to glass manufacturers in more than %2$d states.', 'rian-cullet' ), $years, $states ),
				__( 'Our yard is at Tikri Kalan in West Delhi, on the Rohtak Road corridor at the Delhi–Haryana border. Delhi NCR is one of the largest sources of waste glass in the country, and buying cullet close to where it is generated keeps freight short for plants across North India.', 'rian-cullet' ),
				sprintf( __( 'It is a generational business, founded by Ms. Indu Bhatia, and more than %d companies have sourced cullet from us.', 'rian-cullet' ), $cos ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'Cullet we supply', 'rian-cullet' ),
					'body' => array( __( 'Cullet is supplied by source and by colour, because those two things decide where the glass can go next.', 'rian-cullet' ) ),
					'list' => array(
						__( 'Factory cullet: clean, known-composition glass generated inside glass plants.', 'rian-cullet' ),
						__( 'Foreign (post-consumer) cullet: glass recovered after use, then sorted and processed.', 'rian-cullet' ),
						__( 'Flint (clear / white) cullet, for colourless glass production.', 'rian-cullet' ),
						__( 'Amber (brown) cullet, for amber bottles and jars.', 'rian-cullet' ),
						__( 'Green cullet, for green bottles.', 'rian-cullet' ),
						__( 'Container glass and flat glass cullet, kept apart.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'How supply works', 'rian-cullet' ),
					'list' => array(
						__( 'Tell us what you need: cullet type, colour, the quantity and the delivery location.', 'rian-cullet' ),
						__( 'We confirm availability and agree a dispatch schedule with you.', 'rian-cullet' ),
						__( 'The material is sorted and processed to suit your furnace.', 'rian-cullet' ),
						__( 'It is dispatched by road to your plant, anywhere in India.', 'rian-cullet' ),
					),
					'ordered' => true,
				),
				array(
					'h2'   => __( 'Why manufacturers buy cullet', 'rian-cullet' ),
					'body' => array(
						__( 'Cullet melts at a lower temperature than the virgin batch of sand, soda ash and limestone it replaces. Adding it lowers furnace energy use, extends refractory life and reduces the CO2 released both by burning fuel and by breaking down carbonates in the raw materials.', 'rian-cullet' ),
						__( 'For a glass plant, a dependable cullet supplier is therefore a cost lever as well as a sustainability one. The value depends on consistency: the right colour, kept free of ceramics, stone, metal and organic contamination, delivered when the furnace needs it.', 'rian-cullet' ),
					),
				),
			),
			'faqs' => array(
				array( __( 'Where is Rian Cullet located?', 'rian-cullet' ), __( 'Our registered address is Khasra No. 45/23, 8 Tikri Kalan, Delhi 110041, India, in West Delhi at the Delhi–Haryana border.', 'rian-cullet' ) ),
				array( __( 'Do you supply glass cullet outside Delhi?', 'rian-cullet' ), sprintf( __( 'Yes. We serve customers pan-India and have supplied manufacturers in more than %d states.', 'rian-cullet' ), $states ) ),
				array( __( 'Which types of glass cullet do you supply?', 'rian-cullet' ), __( 'Factory cullet, foreign (post-consumer) cullet and flint cullet, sorted by colour into clear, amber and green, with container and flat glass kept apart.', 'rian-cullet' ) ),
				array( __( 'How do I get a price for glass cullet?', 'rian-cullet' ), __( 'Send an enquiry with the cullet type, colour, quantity and delivery location. Price depends on all four, so we quote per requirement rather than publishing a rate list.', 'rian-cullet' ) ),
				array( __( 'How long has Rian Cullet been in business?', 'rian-cullet' ), sprintf( __( 'Since %d. The business was founded in Delhi by Ms. Indu Bhatia and is run as a family business today.', 'rian-cullet' ), RC_FOUNDED ) ),
			),
			'related' => array( 'flint-glass-cullet', 'amber-glass-cullet', 'green-glass-cullet', 'glass-scrap-buyer-delhi' ),
		),

		/* ------------------------------------------------------------------ */
		'glass-scrap-buyer-delhi' => array(
			'nav'     => __( 'Sell Glass Scrap', 'rian-cullet' ),
			'title'   => __( 'Glass Scrap Buyer in Delhi NCR: Sell Waste Glass', 'rian-cullet' ),
			'meta'    => __( 'Sell broken glass, waste bottles and factory glass scrap in Delhi NCR. Rian Cullet collects glass waste from factories, bottlers, hotels and scrap dealers for recycling.', 'rian-cullet' ),
			'eyebrow' => __( 'Collection / Delhi NCR', 'rian-cullet' ),
			'h1'      => __( 'Sell your glass scrap in Delhi NCR', 'rian-cullet' ),
			'lede'    => __( 'We collect broken glass, waste bottles and factory glass waste, and return it to the manufacturing cycle as cullet.', 'rian-cullet' ),
			'image'   => 'foreign-cullet',
			'alt'     => __( 'Broken post-consumer glass collected for recycling.', 'rian-cullet' ),
			'intro'   => array(
				__( 'Every glass bottle, jar and broken pane is raw material for a glass furnace. Rian Cullet collects that glass across Delhi NCR, sorts it by type and colour, and processes it into cullet that glass manufacturers melt down into new glass.', 'rian-cullet' ),
				sprintf( __( 'We have done this from Tikri Kalan, Delhi, since %d. If your business generates glass waste regularly, or you aggregate it from others, talk to us.', 'rian-cullet' ), RC_FOUNDED ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'Glass we collect', 'rian-cullet' ),
					'list' => array(
						__( 'Waste glass bottles: beer, liquor, wine, soft drink, sauce and pharmaceutical bottles.', 'rian-cullet' ),
						__( 'Glass jars and other container glass.', 'rian-cullet' ),
						__( 'Broken container glass, in clear, amber or green.', 'rian-cullet' ),
						__( 'Factory glass waste: rejects, breakage and process cullet from glass plants and bottling lines.', 'rian-cullet' ),
						__( 'Flat glass waste, such as sheet glass offcuts, kept separate from container glass.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'Glass that usually cannot be recycled with bottles', 'rian-cullet' ),
					'body' => array( __( 'These melt at different temperatures or carry coatings and contaminants that spoil a furnace batch. Ask us before sending them.', 'rian-cullet' ) ),
					'list' => array(
						__( 'Heat-resistant (borosilicate) cookware and laboratory glass.', 'rian-cullet' ),
						__( 'Ceramics, porcelain, crockery and stone mixed in with glass.', 'rian-cullet' ),
						__( 'Light bulbs, tube lights and CFLs.', 'rian-cullet' ),
						__( 'Mirrors, and laminated or wired safety glass.', 'rian-cullet' ),
						__( 'Old television and monitor screens (CRT glass).', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'Who we work with', 'rian-cullet' ),
					'list' => array(
						__( 'Glass manufacturing plants with in-house breakage and rejects.', 'rian-cullet' ),
						__( 'Breweries, distilleries, bottlers and beverage companies.', 'rian-cullet' ),
						__( 'Hotels, restaurants, bars and caterers.', 'rian-cullet' ),
						__( 'Scrap dealers, kabadiwalas and waste aggregators.', 'rian-cullet' ),
						__( 'Material recovery facilities and waste management operators.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'How it works', 'rian-cullet' ),
					'list' => array(
						__( 'Send an enquiry with the type of glass, its colour, the approximate quantity and where it is.', 'rian-cullet' ),
						__( 'We confirm whether we can take it and agree collection or delivery to our Tikri Kalan yard.', 'rian-cullet' ),
						__( 'The glass is checked, sorted by type and colour, and processed into cullet.', 'rian-cullet' ),
						__( 'That cullet is supplied to glass manufacturers and melted into new glass.', 'rian-cullet' ),
					),
					'ordered' => true,
				),
			),
			'faqs' => array(
				array( __( 'Do you accept broken glass?', 'rian-cullet' ), __( 'Yes. Cullet is broken glass by definition, so breakage is not a problem. What matters is that it is glass of the right kind, and as free as possible of ceramics, stone, metal and rubbish.', 'rian-cullet' ) ),
				array( __( 'Do I need to separate glass by colour?', 'rian-cullet' ), __( 'Sorting by colour is part of our work, so mixed loads can be discussed. Glass that is already separated into clear, amber and green is easier to process and is generally more valuable.', 'rian-cullet' ) ),
				array( __( 'Can you collect from outside Delhi?', 'rian-cullet' ), __( 'Our yard is in Delhi and most collection is across Delhi NCR. For regular volumes elsewhere, send an enquiry and we will tell you what is practical.', 'rian-cullet' ) ),
				array( __( 'Do you take light bulbs or tube lights?', 'rian-cullet' ), __( 'No. Lamps contain metals, coatings and in some cases mercury, and are handled through e-waste channels, not glass cullet.', 'rian-cullet' ) ),
				array( __( 'Is there a minimum quantity?', 'rian-cullet' ), __( 'It depends on the material and the location. Tell us what you have and how often it is generated, and we will advise.', 'rian-cullet' ) ),
			),
			'related' => array( 'glass-recycling-company-india', 'glass-cullet-supplier-delhi', 'faq' ),
		),

		/* ------------------------------------------------------------------ */
		'flint-glass-cullet' => array(
			'nav'     => __( 'Flint (Clear) Glass Cullet', 'rian-cullet' ),
			'title'   => __( 'Flint (Clear) Glass Cullet Supplier in India', 'rian-cullet' ),
			'meta'    => sprintf( __( 'Flint (clear / white) glass cullet, sorted away from amber and green for colourless glass production. Supplied pan-India by Rian Cullet, Delhi, since %d.', 'rian-cullet' ), RC_FOUNDED ),
			'eyebrow' => __( 'Cullet / Flint', 'rian-cullet' ),
			'h1'      => __( 'Flint glass cullet', 'rian-cullet' ),
			'lede'    => __( 'Clear, colourless cullet kept apart from coloured glass, for manufacturers of clear bottles, jars and glassware.', 'rian-cullet' ),
			'image'   => 'flint-cullet',
			'alt'     => __( 'Clear flint glass cullet with a slight blue-green edge, sorted from coloured glass.', 'rian-cullet' ),
			'intro'   => array(
				__( 'In the glass trade, "flint" means clear, colourless soda-lime glass, the kind used for most transparent bottles and jars. Flint cullet, also called white cullet, is that glass recovered, sorted and processed for remelting.', 'rian-cullet' ),
				__( 'It is the most demanding colour to supply. A clear furnace is very sensitive to colour: a small amount of amber or green glass in the batch is enough to tint the product. That is why flint cullet has to be sorted and kept apart with more care than any other colour.', 'rian-cullet' ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'Where flint cullet goes', 'rian-cullet' ),
					'list' => array(
						__( 'Clear bottles for spirits, beverages and pharmaceuticals.', 'rian-cullet' ),
						__( 'Food jars and clear containers.', 'rian-cullet' ),
						__( 'Tableware and glassware.', 'rian-cullet' ),
						__( 'Cosmetic and perfume bottles.', 'rian-cullet' ),
					),
					'body_after' => array( __( 'Flint is also the most flexible colour: clear cullet can be added to amber or green production, whereas coloured cullet cannot go the other way.', 'rian-cullet' ) ),
				),
				array(
					'h2'   => __( 'What buyers check', 'rian-cullet' ),
					'list' => array(
						__( 'Colour: no amber or green glass mixed in.', 'rian-cullet' ),
						__( 'Ceramics, stone and porcelain, which do not melt and cause defects in the finished glass.', 'rian-cullet' ),
						__( 'Metals such as caps, rings and foil.', 'rian-cullet' ),
						__( 'Organics: labels, paper, plastic and residue.', 'rian-cullet' ),
						__( 'Glass type: heat-resistant and other non-soda-lime glass kept out.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'Our flint cullet', 'rian-cullet' ),
					'body' => array( sprintf( __( 'We supply flint as factory cullet from glass plants and as post-consumer cullet recovered after use, sorted by type and colour and processed for the furnace. It is dispatched from Delhi to manufacturers across India, as it has been since %d.', 'rian-cullet' ), RC_FOUNDED ) ),
				),
			),
			'faqs' => array(
				array( __( 'What is flint glass?', 'rian-cullet' ), __( 'In glass manufacturing, flint is clear, colourless soda-lime glass. The term is not the same as "flint glass" in optics or lead crystal.', 'rian-cullet' ) ),
				array( __( 'Why is flint cullet called white cullet?', 'rian-cullet' ), __( 'Clear glass looks white when it is crushed, so the trade uses "white" and "flint" interchangeably for clear cullet.', 'rian-cullet' ) ),
				array( __( 'Can flint cullet be used in coloured glass?', 'rian-cullet' ), __( 'Yes. Clear cullet can go into amber or green production. Coloured cullet cannot go into clear glass without tinting it.', 'rian-cullet' ) ),
				array( __( 'Do you supply flint cullet outside Delhi?', 'rian-cullet' ), __( 'Yes, pan-India. Send an enquiry with the quantity and your plant location.', 'rian-cullet' ) ),
			),
			'related' => array( 'amber-glass-cullet', 'green-glass-cullet', 'glass-cullet-supplier-delhi' ),
		),

		/* ------------------------------------------------------------------ */
		'amber-glass-cullet' => array(
			'nav'     => __( 'Amber (Brown) Glass Cullet', 'rian-cullet' ),
			'title'   => __( 'Amber Glass Cullet Supplier in India', 'rian-cullet' ),
			'meta'    => __( 'Amber (brown) glass cullet, sorted by colour for manufacturers of amber bottles and jars. Supplied pan-India from Delhi by Rian Cullet. Send an enquiry.', 'rian-cullet' ),
			'eyebrow' => __( 'Cullet / Amber', 'rian-cullet' ),
			'h1'      => __( 'Amber glass cullet', 'rian-cullet' ),
			'lede'    => __( 'Brown glass cullet, sorted by colour and processed for manufacturers of amber bottles and jars.', 'rian-cullet' ),
			'image'   => 'amber-glass',
			'alt'     => __( 'Amber glass cullet fragments in natural light.', 'rian-cullet' ),
			'intro'   => array(
				__( 'Amber glass is brown glass. Its colour filters out much of the ultraviolet and blue light that can spoil what is inside, which is why it is the standard choice for beer, medicines and many light-sensitive products.', 'rian-cullet' ),
				__( 'Amber cullet is that glass recovered, sorted from clear and green, and processed so it can be remelted into new amber containers.', 'rian-cullet' ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'Where amber glass comes from', 'rian-cullet' ),
					'list' => array(
						__( 'Beer bottles.', 'rian-cullet' ),
						__( 'Pharmaceutical and medicine bottles.', 'rian-cullet' ),
						__( 'Some spirit, sauce and cosmetic bottles.', 'rian-cullet' ),
						__( 'Breakage and rejects from amber container production.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'Why amber is kept separate', 'rian-cullet' ),
					'body' => array( __( 'Amber is produced with a specific batch chemistry, and its colour has to stay consistent from one production run to the next. Clear or green glass mixed into amber cullet shifts that colour, so amber is sorted out of mixed glass and kept as its own stream.', 'rian-cullet' ) ),
				),
				array(
					'h2'   => __( 'Our amber cullet', 'rian-cullet' ),
					'body' => array( sprintf( __( 'We supply amber cullet sorted by type and colour, processed for the furnace and dispatched from Delhi to manufacturers in more than %d states.', 'rian-cullet' ), $states ) ),
				),
			),
			'faqs' => array(
				array( __( 'Why are beer and medicine bottles brown?', 'rian-cullet' ), __( 'Amber glass blocks much of the ultraviolet light that can degrade beer and many medicines, so it protects the contents better than clear glass.', 'rian-cullet' ) ),
				array( __( 'Can amber cullet be used to make clear glass?', 'rian-cullet' ), __( 'No. Amber cullet would tint clear glass, so it is used for amber production.', 'rian-cullet' ) ),
				array( __( 'Do you supply amber cullet across India?', 'rian-cullet' ), __( 'Yes, pan-India from our Delhi yard. Send an enquiry with your quantity and location.', 'rian-cullet' ) ),
			),
			'related' => array( 'flint-glass-cullet', 'green-glass-cullet', 'glass-cullet-supplier-delhi' ),
		),

		/* ------------------------------------------------------------------ */
		'green-glass-cullet' => array(
			'nav'     => __( 'Green Glass Cullet', 'rian-cullet' ),
			'title'   => __( 'Green Glass Cullet Supplier in India', 'rian-cullet' ),
			'meta'    => __( 'Green glass cullet sorted from mixed waste glass and processed for manufacturers of green bottles. Supplied pan-India from Delhi by Rian Cullet.', 'rian-cullet' ),
			'eyebrow' => __( 'Cullet / Green', 'rian-cullet' ),
			'h1'      => __( 'Green glass cullet', 'rian-cullet' ),
			'lede'    => __( 'Green cullet, sorted from mixed waste glass and processed for manufacturers of green bottles.', 'rian-cullet' ),
			'image'   => 'green-glass',
			'alt'     => __( 'Green glass cullet fragments in natural light.', 'rian-cullet' ),
			'intro'   => array(
				__( 'Green glass is used for wine, beer and many other beverage bottles. Green cullet is that glass recovered after use or from production, sorted by colour, and processed so it can be remelted into new green containers.', 'rian-cullet' ),
				__( 'Green furnaces are generally the most tolerant of small amounts of other colours, which makes green cullet an important outlet for recovered glass. Consistency still matters: the closer the cullet is to the target shade, the easier it is for a plant to hold its colour.', 'rian-cullet' ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'Where green glass comes from', 'rian-cullet' ),
					'list' => array(
						__( 'Wine and beer bottles.', 'rian-cullet' ),
						__( 'Some soft drink, spirit and sauce bottles.', 'rian-cullet' ),
						__( 'Breakage and rejects from green container production.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'What makes green cullet usable', 'rian-cullet' ),
					'list' => array(
						__( 'Sorted by colour, with clear and amber removed.', 'rian-cullet' ),
						__( 'Free of ceramics, stone and porcelain.', 'rian-cullet' ),
						__( 'Metals such as caps and rings removed.', 'rian-cullet' ),
						__( 'Labels, paper and residue kept to a minimum.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'Our green cullet', 'rian-cullet' ),
					'body' => array( sprintf( __( 'We supply green cullet sorted by type and colour and processed for the furnace, from Delhi to manufacturers across India, as we have since %d.', 'rian-cullet' ), RC_FOUNDED ) ),
				),
			),
			'faqs' => array(
				array( __( 'Why is green glass sorted separately?', 'rian-cullet' ), __( 'Each colour is made with its own batch chemistry. Sorting keeps the colour of new glass consistent and lets each colour go back into the production that needs it.', 'rian-cullet' ) ),
				array( __( 'Can mixed-colour glass be used in green production?', 'rian-cullet' ), __( 'Green production can usually accept a small share of other colours, more than clear production can, but the exact tolerance is set by each manufacturer.', 'rian-cullet' ) ),
				array( __( 'Do you supply green cullet across India?', 'rian-cullet' ), __( 'Yes, pan-India from Delhi. Send an enquiry with your quantity and plant location.', 'rian-cullet' ) ),
			),
			'related' => array( 'flint-glass-cullet', 'amber-glass-cullet', 'glass-cullet-supplier-delhi' ),
		),

		/* ------------------------------------------------------------------ */
		'glass-recycling-company-india' => array(
			'nav'     => __( 'Glass Recycling in India', 'rian-cullet' ),
			'title'   => __( 'Glass Recycling Company in India', 'rian-cullet' ),
			'meta'    => sprintf( __( 'How glass recycling works in India, from waste bottles to furnace-ready cullet, and how Rian Cullet has collected, sorted and processed glass in Delhi since %d.', 'rian-cullet' ), RC_FOUNDED ),
			'eyebrow' => __( 'Recycling / India', 'rian-cullet' ),
			'h1'      => __( 'Glass recycling in India', 'rian-cullet' ),
			'lede'    => sprintf( __( 'How waste glass becomes raw material again, and where Rian Cullet fits in that chain, from Delhi since %d.', 'rian-cullet' ), RC_FOUNDED ),
			'image'   => 'processing-sorting',
			'alt'     => __( 'Recovered glass being sorted before processing into cullet.', 'rian-cullet' ),
			'image_ratio' => 'rc-ratio-editorial',
			'image_w' => 1800,
			'image_h' => 1200,
			'intro'   => array(
				__( 'Glass is one of the few materials that can be recycled again and again without losing quality. A bottle melted down becomes a new bottle, as clear and as strong as the first. The material that makes this possible is cullet: recovered glass, sorted and processed so a furnace can use it.', 'rian-cullet' ),
				sprintf( __( 'Rian Cullet is a glass recycling company based in Delhi. Since %1$d we have collected, sorted and processed waste glass and supplied it as cullet to glass manufacturers in more than %2$d states.', 'rian-cullet' ), RC_FOUNDED, $states ),
			),
			'sections' => array(
				array(
					'h2'   => __( 'How the chain works', 'rian-cullet' ),
					'list' => array(
						__( 'Generation: glass is used and discarded by households, hotels, bars, bottlers and factories.', 'rian-cullet' ),
						__( 'Collection: in India much of it is gathered by kabadiwalas, scrap dealers and aggregators, and some by waste management operators.', 'rian-cullet' ),
						__( 'Sorting and processing: a cullet processor such as Rian Cullet sorts it by type and colour, removes contamination and prepares it for the furnace.', 'rian-cullet' ),
						__( 'Remelting: glass manufacturers add the cullet to their batch and melt it into new glass.', 'rian-cullet' ),
					),
					'ordered' => true,
				),
				array(
					'h2'   => __( 'Why it matters', 'rian-cullet' ),
					'body' => array(
						__( 'Cullet melts at a lower temperature than the sand, soda ash and limestone it replaces. Every tonne used means less raw material quarried, less fuel burned in the furnace and less CO2 released, both from the fuel and from the carbonates in the raw batch.', 'rian-cullet' ),
						__( 'Industry sources commonly put the energy saving at roughly 2 to 3 percent for every 10 percent of cullet in the batch. For a glass plant that is a real cost saving; for a city it is glass kept out of landfill.', 'rian-cullet' ),
					),
				),
				array(
					'h2'   => __( 'What makes recycled glass usable', 'rian-cullet' ),
					'body' => array( __( 'Recycling glass well is mostly about sorting. A furnace needs cullet of the right colour and type, free of anything that will not melt cleanly:', 'rian-cullet' ) ),
					'list' => array(
						__( 'Colour sorted into clear (flint), amber and green.', 'rian-cullet' ),
						__( 'Container glass kept apart from flat glass.', 'rian-cullet' ),
						__( 'Ceramics, stone and porcelain removed.', 'rian-cullet' ),
						__( 'Metals, labels and organic residue removed.', 'rian-cullet' ),
						__( 'Heat-resistant glass, bulbs and screens kept out.', 'rian-cullet' ),
					),
				),
			),
			'faqs' => array(
				array( __( 'Is glass 100 percent recyclable?', 'rian-cullet' ), __( 'Container glass can be remelted repeatedly without loss of quality, so in principle it is fully recyclable. In practice what limits it is collection and contamination, which is why sorting matters.', 'rian-cullet' ) ),
				array( __( 'Why is recycled glass called cullet?', 'rian-cullet' ), __( 'Cullet is the glass industry\'s term for broken or waste glass prepared for remelting, whether it comes from inside a factory or from used bottles and jars.', 'rian-cullet' ) ),
				array( __( 'What happens to glass after it is recycled?', 'rian-cullet' ), __( 'It is sorted, processed into cullet and melted with raw materials in a glass furnace to make new bottles, jars and other glass products.', 'rian-cullet' ) ),
				array( __( 'Which glass cannot be recycled with bottles?', 'rian-cullet' ), __( 'Heat-resistant cookware, ceramics, light bulbs and tube lights, mirrors, laminated or wired glass and old screens melt differently or carry contaminants, so they are kept out of container glass recycling.', 'rian-cullet' ) ),
			),
			'related' => array( 'glass-scrap-buyer-delhi', 'glass-cullet-supplier-delhi', 'faq' ),
		),

		/* ------------------------------------------------------------------ */
		'faq' => array(
			'nav'     => __( 'Glass Cullet FAQ', 'rian-cullet' ),
			'title'   => __( 'Glass Cullet FAQ: Types, Colours and Supply', 'rian-cullet' ),
			'meta'    => __( 'Answers to common questions about glass cullet: what it is, factory vs foreign cullet, flint, amber and green, quality, pricing and pan-India supply from Delhi.', 'rian-cullet' ),
			'eyebrow' => __( 'Questions', 'rian-cullet' ),
			'h1'      => __( 'Glass cullet: frequently asked questions', 'rian-cullet' ),
			'lede'    => __( 'Short answers to the questions buyers and sellers of glass cullet ask us most often.', 'rian-cullet' ),
			'image'   => '',
			'intro'   => array(),
			'sections' => array(),
			'faqs' => array(
				array( __( 'What is glass cullet?', 'rian-cullet' ), __( 'Glass cullet is recycled or broken glass that has been processed and prepared for melting and reuse in the production of new glass.', 'rian-cullet' ) ),
				array( __( 'What is the difference between factory cullet and foreign cullet?', 'rian-cullet' ), __( 'Factory cullet is glass waste generated inside a glass plant, so its composition is known. Foreign cullet, also called post-consumer cullet, is glass recovered after use, from bottles, jars and other discarded glass, and needs more sorting and processing.', 'rian-cullet' ) ),
				array( __( 'What is flint cullet?', 'rian-cullet' ), __( 'Flint cullet is clear, colourless glass cullet, also called white cullet, kept apart from amber and green for colourless glass production.', 'rian-cullet' ) ),
				array( __( 'Why is cullet sorted by colour?', 'rian-cullet' ), __( 'Colour decides where glass can go next. Clear glass production is very sensitive to coloured glass, so cullet is sorted into clear, amber and green and each goes back into matching production.', 'rian-cullet' ) ),
				array( __( 'Why do glass manufacturers use cullet?', 'rian-cullet' ), __( 'Cullet melts at a lower temperature than virgin raw materials, which reduces furnace energy use and CO2 emissions and cuts the amount of sand, soda ash and limestone needed.', 'rian-cullet' ) ),
				array( __( 'What contamination matters most in cullet?', 'rian-cullet' ), __( 'Ceramics, stone and porcelain, which do not melt; metals such as caps and rings; organic material such as labels and residue; wrong colours; and non-soda-lime glass such as heat-resistant cookware.', 'rian-cullet' ) ),
				array( __( 'What is the price of glass cullet?', 'rian-cullet' ), __( 'Price depends on the type, colour, quantity and delivery location, and moves with the market. Send an enquiry with your requirement for a quote.', 'rian-cullet' ) ),
				array( __( 'Where is Rian Cullet based?', 'rian-cullet' ), __( 'At Khasra No. 45/23, 8 Tikri Kalan, Delhi 110041, India.', 'rian-cullet' ) ),
				array( __( 'Do you supply across India?', 'rian-cullet' ), sprintf( __( 'Yes. We serve customers pan-India and have supplied more than %1$d companies in more than %2$d states.', 'rian-cullet' ), $cos, $states ) ),
				array( __( 'Do you buy glass scrap?', 'rian-cullet' ), __( 'We collect waste glass, including broken bottles and factory glass waste, across Delhi NCR. Send an enquiry with the type, colour, quantity and location.', 'rian-cullet' ) ),
				array( __( 'When was Rian Cullet established?', 'rian-cullet' ), sprintf( __( 'In %d, in Delhi, founded by Ms. Indu Bhatia.', 'rian-cullet' ), RC_FOUNDED ) ),
			),
			'related' => array( 'glass-cullet-supplier-delhi', 'glass-scrap-buyer-delhi', 'glass-recycling-company-india' ),
		),
	);

	/**
	 * Filters the search landing pages.
	 *
	 * @param array<string, array<string, mixed>> $pages Page definitions.
	 */
	return (array) apply_filters( 'rc_seo_pages', $pages );
}

/**
 * The landing page definition for the current request, if any.
 *
 * @return array<string, mixed>|null
 */
function rc_current_seo_page(): ?array {

	if ( ! is_page() ) {
		return null;
	}

	$slug  = (string) get_post_field( 'post_name', get_queried_object_id() );
	$pages = rc_seo_pages();

	if ( ! isset( $pages[ $slug ] ) ) {
		return null;
	}

	return array_merge( $pages[ $slug ], array( 'slug' => $slug ) );
}

add_filter( 'template_include', 'rc_seo_page_template' );
/**
 * Render landing pages with the shared template.
 *
 * @param string $template Resolved template.
 * @return string
 */
function rc_seo_page_template( string $template ): string {
	if ( rc_current_seo_page() ) {
		$custom = RC_DIR . '/seo-landing.php';
		if ( file_exists( $custom ) ) {
			return $custom;
		}
	}
	return $template;
}

add_filter( 'rc_meta_description', 'rc_seo_page_description' );
/**
 * Landing page meta descriptions.
 *
 * @param string $description Description.
 * @return string
 */
function rc_seo_page_description( string $description ): string {
	$page = rc_current_seo_page();
	return $page ? (string) $page['meta'] : $description;
}

add_filter( 'document_title_parts', 'rc_seo_page_title', 20 );
/**
 * Landing page <title>: the keyword-led title, then the brand.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function rc_seo_page_title( array $parts ): array {
	if ( rc_seo_plugin_active() ) {
		return $parts;
	}
	$page = rc_current_seo_page();
	if ( $page ) {
		return array(
			'title' => (string) $page['title'],
			'site'  => get_bloginfo( 'name' ),
		);
	}
	return $parts;
}

/**
 * URL of a landing page by slug.
 *
 * @param string $slug Slug.
 * @return string
 */
function rc_seo_page_url( string $slug ): string {
	return home_url( '/' . $slug . '/' );
}
