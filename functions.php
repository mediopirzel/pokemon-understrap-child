<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;



/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );



/**
 * Enqueue our stylesheet and javascript file
 */
function theme_enqueue_styles() {

	// Get the theme data.
	$the_theme     = wp_get_theme();
	$theme_version = $the_theme->get( 'Version' );

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	// Grab asset urls.
	$theme_styles  = "/css/child-theme{$suffix}.css";
	$theme_scripts = "/js/child-theme{$suffix}.js";
	
	$css_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_styles );

	wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . $theme_styles, array(), $css_version );
	wp_enqueue_script( 'jquery' );
	
	$js_version = $theme_version . '.' . filemtime( get_stylesheet_directory() . $theme_scripts );
	
	wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . $theme_scripts, array(), $js_version, true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );



/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

/**
 *  Pokemon custom post type scripts
 */

 function pokemon_files() {
	wp_enqueue_script('pokemon-ajax', get_theme_file_uri('/js/pokemon-ajax.js'), array('jquery'), '1.0', true);
	wp_enqueue_script('pokemon-insert', get_theme_file_uri('/js/pokemon-insert.js'), false, '1.0', true);
  
	wp_localize_script('pokemon-ajax','pokemonData',array(
	  'ajaxurl'=> admin_url('admin-ajax.php'),
	  'nonce'=> wp_create_nonce('wp_rest'),
	  'loadedText'=> __('Loaded')
	));

	wp_localize_script('pokemon-insert','wordpressData',array(
		'root_url'=> get_site_url(),
		'nonce'=> wp_create_nonce('wp_rest'),
	  ));


  }
  
  add_action('wp_enqueue_scripts', 'pokemon_files');


/**
 *  Get data from ajax call in pokemon-ajax.js
 */
add_action('wp_ajax_nopriv_pokemon-ajax','pokemon_send_content');
add_action('wp_ajax_pokemon-ajax','pokemon_send_content');

function pokemon_send_content()
{

	$id_post = absint($_POST['id_post']);
	// Return oldest pokemon index number and its version name. 
	$content = get_post_meta( $id_post, 'pokemon_podekedex_num_old' )[0].' ( '.get_post_meta( $id_post, 'pokemon_podekedex_name_old' )[0].' )';

	echo $content;

   
	wp_die();
}


include_once dirname(__FILE__).'/inc/pokemon-custom-post-types.php';
include_once dirname(__FILE__).'/inc/pokemon-endpoints.php';