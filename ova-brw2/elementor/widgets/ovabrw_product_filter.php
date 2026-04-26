<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Filter
 */
if ( !class_exists( 'OVABRW_Widget_Product_Filter' ) ) {

	class OVABRW_Widget_Product_Filter extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_filter';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Products Slider', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-products';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-products' ];
		}

		/**
		 * Get script depends
		 */
		public function get_script_depends() {
			return [ 'ova-fancybox', 'swiper', 'ovabrw-product-filter' ];
		}

		/**
		 * Get style depends
		 */
		public function get_style_depends() {
			$style_depends = [
				'ova-fancybox',
				'swiper'
			];

			// BRW icon
		    if ( apply_filters( OVABRW_PREFIX.'use_brwicon', true ) ) {
		    	$style_depends[] = 'ovabrw-icon';
		    }

		    // Product filter
		    $style_depends[] = 'ovabrw-product-filter';

		    return $style_depends;
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

				// Default card template
				$default_card = [
					'' => esc_html__( 'Default', 'ova-brw')
				];

				// Get card templates
				$card_templates = ovabrw_get_card_templates();
				if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];

				$this->add_control(
					'card_template',
					[
						'label' 	=> esc_html__( 'Card template', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'card1',
						'options' 	=> array_merge( $default_card, $card_templates ),
					]
				);

				// Product categories
				$product_categories = [];

				// Default categories
				$default_categories = [];

				// Get categories
				$categories = get_categories([
					'taxonomy' 	=> 'product_cat',
					'orderby' 	=> 'name',
					'order' 	=> 'ASC'
				]);

				// Loop
			  	if ( ovabrw_array_exists( $categories ) ) {
				  	foreach ( $categories as $i => $category ) {
					  	$product_categories[$category->term_id] = $category->name;

					  	// Default categories
					  	if ( $i <= 3 ) {
					  		array_push( $default_categories, $category->term_id );
					  	}
				  	}
			  	} // END loop

			  	$this->add_control(
					'categories',
					[
						'label' 		=> esc_html__( 'Select Category', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $product_categories,
						'default' 		=> $default_categories,
					]
				);

				$this->add_control(
					'posts_per_page',
					[
						'label'   => esc_html__( 'Posts per page', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'min'     => -1,
						'default' => 6,
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' 	=> esc_html__( 'Order By', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'date',
						'options' 	=> [
							'ID'  			=> esc_html__( 'ID', 'ova-brw' ),
							'title' 		=> esc_html__( 'Title', 'ova-brw' ),
							'date' 			=> esc_html__( 'Date', 'ova-brw' ),
							'modified' 		=> esc_html__('Modified', 'ova-brw'),
							'rand' 			=> esc_html__('Random', 'ova-brw'),
							'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' )
						],
					]
				);

				$this->add_control(
					'order',
					[
						'label' 	=> esc_html__( 'Order', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'DESC',
						'options' 	=> [
							'ASC'  	=> esc_html__( 'Ascending', 'ova-brw' ),
							'DESC'  => esc_html__( 'Descending', 'ova-brw' ),
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_additional_options',
				[
					'label' => esc_html__( 'Additional Options Slider', 'ova-brw' ),
				]
			);

				$this->add_control(
					'item_number',
					[
						'label' 	=> esc_html__( 'Item number', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'default' 	=> 3,
					]
				);

				$this->add_control(
					'slides_to_scroll',
					[
						'label'       => esc_html__( 'Slides to Scroll', 'ova-brw' ),
						'type'        => \Elementor\Controls_Manager::NUMBER,
						'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'ova-brw' ),
						'default'     => 1,
					]
				);

				$this->add_control(
					'margin_item',
					[
						'label' 	=> esc_html__( 'Margin item', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'default' 	=> 25,
					]
				);

				$this->add_control(
					'pause_on_hover',
					[
						'label'   => esc_html__( 'Pause on Hover', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'yes',
						'options' => [
							'yes' => esc_html__( 'Yes', 'ova-brw' ),
							'no'  => esc_html__( 'No', 'ova-brw' ),
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'infinite',
					[
						'label'   => esc_html__( 'Infinite Loop', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'false',
						'options' => [
							'yes' => esc_html__( 'Yes', 'ova-brw' ),
							'no'  => esc_html__( 'No', 'ova-brw' ),
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'autoplay',
					[
						'label'   => esc_html__( 'Autoplay', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'false',
						'options' => [
							'yes' => esc_html__( 'Yes', 'ova-brw' ),
							'no'  => esc_html__( 'No', 'ova-brw' ),
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'autoplay_speed',
					[
						'label'     => esc_html__( 'Autoplay Speed', 'ova-brw' ),
						'type'      => \Elementor\Controls_Manager::NUMBER,
						'default'   => 3000,
						'step'      => 500,
						'condition' => [
							'autoplay' => 'yes',
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'smartspeed',
					[
						'label'   => esc_html__( 'Smart Speed', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'default' => 500,
					]
				);

				$this->add_control(
					'nav_control',
					[
						'label'   => esc_html__( 'Show Nav', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'yes',
						'options' => [
							'yes' => esc_html__( 'Yes', 'ova-brw' ),
							'no'  => esc_html__( 'No', 'ova-brw' ),
						],
						'frontend_available' => true,
					]
				);

				$this->add_control(
					'dots_control',
					[
						'label'   => esc_html__( 'Show Dots', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SWITCHER,
						'default' => 'yes',
						'options' => [
							'yes' => esc_html__( 'Yes', 'ova-brw' ),
							'no'  => esc_html__( 'No', 'ova-brw' ),
						],
						'frontend_available' => true,
					]
				);

			$this->end_controls_section();
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			// Get card template
			$card_template = ovabrw_get_meta_data( 'card_template', $settings, 'card1' );

			// Responsive
			$breakpoints = [
				'0' => [
					'slidesPerView' => 1
				],
	        	'768' => [
	        		'slidesPerView' => 2
	        	],
	        	'1024' => [
	        		'slidesPerView' => 3
	        	],
	        	'1200' => [
	        		'slidesPerView' => absint( ovabrw_get_meta_data( 'item_number', $settings, 3 ) )
	        	]
			];

			// For card 5 & card 6
			if ( in_array( $card_template, ['card5', 'card6'] ) ) {
				$breakpoints = [
					'0' => [
						'slidesPerView' => 1
					],
		        	'768' => [
		        		'slidesPerView' => 1
		        	],
		        	'1024' => [
		        		'slidesPerView' => 1
		        	],
		        	'1200' => [
		        		'slidesPerView' => 1
		        	]
				];
			}

			// Arguments
			$args = [
				'template' 			=> $card_template,
				'categories' 		=> ovabrw_get_meta_data( 'categories', $settings ),
				'posts_per_page' 	=> absint( ovabrw_get_meta_data( 'posts_per_page', $settings, 6 ) ),
				'orderby' 			=> ovabrw_get_meta_data( 'orderby', $settings, 'date' ),
				'order' 			=> ovabrw_get_meta_data( 'order', $settings, 'DESC' ),
				'slide_options' 	=> [
					'slidesPerView' 	=> absint( ovabrw_get_meta_data( 'item_number', $settings, 3 ) ),
					'slidesPerGroup' 	=> ovabrw_get_meta_data( 'slides_to_scroll', $settings ),
					'spaceBetween' 		=> ovabrw_get_meta_data( 'margin_item', $settings ),
					'autoplay' 			=> 'yes' === ovabrw_get_meta_data( 'autoplay', $settings ) ? true : false,
					'pauseOnMouseEnter' => 'yes' === ovabrw_get_meta_data( 'pause_on_hover', $settings ) ? true : false,
					'delay' 			=> ovabrw_get_meta_data( 'autoplay_speed', $settings ),
					'speed' 			=> ovabrw_get_meta_data( 'smartspeed', $settings ),
					'loop' 				=> 'yes' === ovabrw_get_meta_data( 'infinite', $settings ) ? true : false,
					'nav' 					=> 'yes' === ovabrw_get_meta_data( 'nav_control', $settings ) ? true : false,
					'dots' 					=> 'yes' === ovabrw_get_meta_data( 'dots_control', $settings ) ? true : false,
					'rtl' 					=> is_rtl() ? true : false,
					'breakpoints' 			=> $breakpoints
				],
			];

			// Get template
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_filter', 'elementor/ovabrw-product-filter.php', $settings ), $args );
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Filter() );
}