<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Appointment_Popup_Button
 */
if ( !class_exists( 'OVABRW_Widget_Appointment_Popup_Button' ) ) {

	class OVABRW_Widget_Appointment_Popup_Button extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_appointment_popup_button';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Appointment Popup Button', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-button';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-product' ];
		}

		/**
		 * Enqueue script
		 */
		public function get_script_depends() {
			return [ 'ovabrw-appointment-popup-button' ];
		}

		/**
		 * Enqueue style
		 */
		public function get_style_depends() {
			return [ 'ovabrw-appointment-popup-button' ];
		}

		/**
		 * Register control
		 */
		protected function register_controls() {
			// Section content
			$this->start_controls_section(
				'section_content',
				[
					'label'	=> esc_html__( 'Content', 'ova-brw' )
				]
			);	

				$this->add_control(
					'text_button',
					[
						'label'		=> esc_html__( 'Text Button', 'ova-brw' ),
						'type'		=> \Elementor\Controls_Manager::TEXT,
						'default'	=> esc_html__( 'Book Appointment', 'ova-brw' )
					]
				);

				// init
				$product_ids = [];

				// Default product
				$default_product = '';

				// Get rental products with appointment type
				$rental_products = OVABRW()->options->get_rental_product_ids( [ 'type' => 'appointment' ] );
				if ( ovabrw_array_exists( $rental_products ) ) {
					foreach ( $rental_products as $pid ) {
						$product_ids[ $pid ] = get_the_title( $pid );

						// Default product
						if ( !$default_product ) $default_product = $pid;
					}
				}

				if ( empty( $product_ids ) ) {
					$product_ids[ '' ] = esc_html__( 'There are no appointment products', 'ova-brw' );
				}

				$this->add_control(
					'product_id',
					[
						'label'			=> esc_html__( 'Choose Appointment Product', 'ova-brw' ),
						'type'			=> \Elementor\Controls_Manager::SELECT2,
						'label_block'	=> true,
						'options'		=> $product_ids,
						'default'		=> $default_product
					]
				);

				$this->add_control(
				    'icon_align',
				    [
				        'label' 	=> esc_html__( 'Icon Position', 'ova-brw' ),
				        'type' 		=> \Elementor\Controls_Manager::CHOOSE,
				        'options' 	=> [
				            'before' 	=> [
				                'title' => esc_html__( 'Before', 'ova-brw' ),
				                'icon' 	=> 'eicon-arrow-left',
				            ],
				            'after' 	=> [
				                'title' => esc_html__( 'After', 'ova-brw' ),
				                'icon' 	=> 'eicon-arrow-right',
				            ],
				        ],
				        'default' 	=> 'before',
				        'toggle' 	=> false,
				        'condition' => [
				            'icon_button[value]!' => '',
				        ],
				    ]
				);

				$this->add_control(
					'icon_button',
					[
						'label'		=> esc_html__( 'Icon Button', 'ova-brw' ),
						'type'		=> \Elementor\Controls_Manager::ICONS,
						'default'	=> [
							'value'		=> 'far fa-calendar-alt',
							'library'	=> 'fa-regular'
						]
					]
				);

			$this->end_controls_section();

			// Section style
			$this->start_controls_section(
				'section_style',
				[
					'label'	=> esc_html__( 'Button Style', 'ova-brw' ),
					'tab'	=> \Elementor\Controls_Manager::TAB_STYLE
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name'		=> 'button_typography',
						'label'		=> esc_html__( 'Typography', 'ova-brw' ),
						'selector'	=> '{{WRAPPER}} .ovabrw-appointment-button'
					]
				);
				$this->add_control(
				    'icon_size',
				    [
				        'label' 		=> esc_html__( 'Icon Size', 'ova-brw' ),
				        'type'  		=> \Elementor\Controls_Manager::SLIDER,
				        'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
				        'default' 		=> [
				            'unit' => 'px',
				            'size' => 20,
				        ],
				        'selectors' 	=> [
				            '{{WRAPPER}} .ovabrw-appointment-button i' => 'font-size: {{SIZE}}{{UNIT}};',
				            '{{WRAPPER}} .ovabrw-appointment-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				        ],
				    ]
				);

				$this->add_responsive_control(
					'gap',
					[
						'label'			=> esc_html__( 'Gap', 'ova-brw' ),
						'type'			=> \Elementor\Controls_Manager::SLIDER,
						'size_units'	=> [ 'px' ],
						'range'			=> [
							'min' 	=> 0,
							'max'	=> 100,
							'step'	=> 1,
						],
						'selectors'		=> [
							'{{WRAPPER}} .ovabrw-appointment-button' => 'gap: {{SIZE}}{{UNIT}}',
						]
					]
				);

				$this->start_controls_tabs(
					'button_tabs'
				);

					$this->start_controls_tab(
						'button_style_normal_tab',
						[
							'label'	=> esc_html__( 'Normal', 'ova-brw' )
						]
					);

						$this->add_control(
						    'button_color',
						    [
						        'label'     => esc_html__( 'Text Color', 'ova-brw' ),
						        'type'      => \Elementor\Controls_Manager::COLOR,
						        'selectors' => [
						            '{{WRAPPER}} .ovabrw-appointment-button' => 'color: {{VALUE}};',
						        ],
						    ]
						);

						$this->add_control(
							'button_bg_color',
							[
								'label'		=> esc_html__( 'Background Color', 'ova-brw' ),
								'type'		=> \Elementor\Controls_Manager::COLOR,
								'selectors'	=> [
									'{{WRAPPER}} .ovabrw-appointment-button' => 'background-color: {{VALUE}}'
								]
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'button_style_hover_tab',[
							'label'	=> esc_html__( 'Hover', 'ova-brw' )
						]
					);

						$this->add_control(
						    'button_color_hover',
						    [
						        'label'     => esc_html__( 'Text Color', 'ova-brw' ),
						        'type'      => \Elementor\Controls_Manager::COLOR,
						        'selectors' => [
						        	'{{WRAPPER}} .ovabrw-appointment-button:hover' => 'color: {{VALUE}}'
						        ],
						    ]
						);

						$this->add_control(
							'button_bg_color_hover',
							[
								'label'		=> esc_html__( 'Background Color', 'ova-brw' ),
								'type'		=> \Elementor\Controls_Manager::COLOR,
								'selectors'	=> [
									'{{WRAPPER}} .ovabrw-appointment-button:hover' => 'background-color: {{VALUE}}'
								]
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name'		=> 'button_border',
						'label'		=> esc_html__( 'Border', 'ova-brw' ),
						'selector'	=> '{{WRAPPER}} .ovabrw-appointment-button'
					]
				);

				$this->add_control(
					'button_border_radius',
					[
						'label'			=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type'			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units'	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'		=> [
							'{{WRAPPER}} .ovabrw-appointment-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
						]
					]
				);

				$this->add_control(
					'button_padding',
					[
						'label'			=> esc_html__( 'Padding', 'ova-brw' ),
						'type'			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units'	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'		=> [
							'{{WRAPPER}} .ovabrw-appointment-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}'
						]
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

			ob_start();
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_appointment_popup_button', 'elementor/ovabrw-appointment-popup-button.php', $settings ), $settings );
			echo ob_get_clean();
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Appointment_Popup_Button() );
}
