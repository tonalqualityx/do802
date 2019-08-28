<?php
/**
 * Understrap Theme Customizer
 *
 * @package understrap
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
if ( ! function_exists( 'understrap_customize_register' ) ) {
	/**
	 * Register basic customizer support.
	 *
	 * @param object $wp_customize Customizer reference.
	 */
	function understrap_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
}
// add_action( 'customize_register', 'understrap_customize_register' );

if ( ! function_exists( 'understrap_theme_customize_register' ) ) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function understrap_theme_customize_register( $wp_customize ) {

		// Theme layout settings.
		$wp_customize->add_section( 'understrap_theme_layout_options', array(
			'title'       => __( 'Theme Layout Settings', 'understrap' ),
			'capability'  => 'edit_theme_options',
			'description' => __( 'Container width and sidebar defaults', 'understrap' ),
			'priority'    => 160,
		) );

		$wp_customize->add_setting( 'understrap_container_type', array(
			'default'           => 'container',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_textarea',
			'capability'        => 'edit_theme_options',
		) );

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'container_type', array(
					'label'       => __( 'Container Width', 'understrap' ),
					'description' => __( "Choose between Bootstrap's container and container-fluid", 'understrap' ),
					'section'     => 'understrap_theme_layout_options',
					'settings'    => 'understrap_container_type',
					'type'        => 'select',
					'choices'     => array(
						'container'       => __( 'Fixed width container', 'understrap' ),
						'container-fluid' => __( 'Full width container', 'understrap' ),
					),
					'priority'    => '10',
				)
			) );

		$wp_customize->add_setting( 'understrap_sidebar_position', array(
			'default'           => 'right',
			'type'              => 'theme_mod',
			'sanitize_callback' => 'esc_textarea',
			'capability'        => 'edit_theme_options',
		) );

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_sidebar_position', array(
					'label'       => __( 'Sidebar Positioning', 'understrap' ),
					'description' => __( "Set sidebar's default position. Can either be: right, left, both or none. Note: this can be overridden on individual pages.",
					'understrap' ),
					'section'     => 'understrap_theme_layout_options',
					'settings'    => 'understrap_sidebar_position',
					'type'        => 'select',
					'choices'     => array(
						'right' => __( 'Right sidebar', 'understrap' ),
						'left'  => __( 'Left sidebar', 'understrap' ),
						'both'  => __( 'Left & Right sidebars', 'understrap' ),
						'none'  => __( 'No sidebar', 'understrap' ),
					),
					'priority'    => '20',
				)
			) );
	}
} // endif function_exists( 'understrap_theme_customize_register' ).
// add_action( 'customize_register', 'understrap_theme_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if ( ! function_exists( 'understrap_customize_preview_js' ) ) {
	/**
	 * Setup JS integration for live previewing.
	 */
	function understrap_customize_preview_js() {
		wp_enqueue_script( 'understrap_customizer', get_template_directory_uri() . '/js/customizer.js',
			array( 'customize-preview' ), '20130508', true );
	}
}
// add_action( 'customize_preview_init', 'understrap_customize_preview_js' );

//Do802 Customizer options
add_action( 'customize_register', 'do802_customizer_settings' );
function do802_customizer_settings( $wp_customize ) {
	$wp_customize->add_section( 'deals_section' , array(
	    'title'      => 'Deals',
	    'priority'   => 1,
	));
	$wp_customize->add_setting( 'hide_deal_calc' , array(
	    'default'     => 2,
	    'transport'   => 'refresh',
	));
	$wp_customize->add_control( 'hide_deal_calc', array(
		'label'        => 'Expired deals should remain visible for:',
		'section'    => 'deals_section',
		'settings'   => 'hide_deal_calc',
		'type'		=> 'number',	
	));
	$wp_customize->add_setting( 'hide_deal_term' , array(
	    'default'     => 'days',
	    'transport'   => 'refresh',
	));
	$wp_customize->add_control( 'hide_deal_term', array(
		'label'        => '',
		'section'    => 'deals_section',
		'settings'   => 'hide_deal_term',
		'type'		=> 'select',
		'choices' => array(
			'hours'	=> 'Hours',
			'days'	=> 'Days',
		),	
	));
	$wp_customize->add_setting( 'show_flag_count' , array(
	    'default'     => 24,
	    'transport'   => 'refresh',
	));
	$wp_customize->add_control( 'show_flag_count', array(
		'label'        => 'Highlight deals that are expiring within:',
		'section'    => 'deals_section',
		'settings'   => 'show_flag_count',
		'type'		=> 'number',	
	));
	$wp_customize->add_setting( 'show_flag_terms' , array(
	    'default'     => 'hours',
	    'transport'   => 'refresh',
	));
	$wp_customize->add_control( 'show_flag_terms', array(
		'label'        => '',
		'section'    => 'deals_section',
		'settings'   => 'show_flag_terms',
		'type'		=> 'select',
		'choices' => array(
			'hours'	=> 'Hours',
			'days'	=> 'Days',
		),		
	));
}