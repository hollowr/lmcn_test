<?php
/**
 * lmcn functions file
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_excerpt', 'wpautop' );

/******************************************************************************\
	Theme support, standard settings, menus and widgets
\******************************************************************************/

add_theme_support( 'post-formats', array( 'image', 'quote', 'status', 'link' ) );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );

$custom_header_args = array(
	'width'         => 200,
	'height'        => 113,
	'default-image' => get_template_directory_uri() . '/images/logo.png',
);
add_theme_support( 'custom-header', $custom_header_args );
if (function_exists('add_image_size')) {
   add_image_size( 'dummy-1', 200, 113, true );
   add_image_size( 'dummy-2', 480, 0 );
}
 
global $_wp_additional_image_sizes;
foreach ( $_wp_additional_image_sizes as $name => $image_size ){
    update_option( $name."_size_w", $image_size['width'] );
    update_option( $name."_size_h", $image_size['height'] );
    update_option( $name."_crop", $image_size['crop'] );
}
 
add_filter( 'intermediate_image_sizes', 'regenerate_custom_image_sizes' );
function regenerate_custom_image_sizes( $sizes ){
    global $_wp_additional_image_sizes;
    foreach ( $_wp_additional_image_sizes as $name => $size ){
        $sizes[] = $name;
    }
    return $sizes;
}
function new_excerpt_length($length) {
	return 16;
}
add_filter('excerpt_length', 'new_excerpt_length');
function trim_excerpt($text) {
	return rtrim($text,'[Translate]');
}
//add_filter('get_the_excerpt', 'trim_excerpt');

function custom_wp_trim_excerpt($text) {
	$raw_excerpt = $text;
	if ( '' == $text ) {
		//Retrieve the post content.
		$text = get_the_content('');

		//Delete all shortcode tags from the content.
		$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
		$allowed_tags = ''; /*** MODIFY THIS. Add the allowed HTML tags separated by a comma.***/
		$text = strip_tags($text, $allowed_tags);

		$excerpt_word_count = 7; /*** MODIFY THIS. change the excerpt word count to any integer you like.***/
		$excerpt_length = apply_filters('excerpt_length', $excerpt_word_count);

		/***$excerpt_end = '[...]';  MODIFY THIS. change the excerpt endind to something else.***/
		$excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);

		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'custom_wp_trim_excerpt');

function new_excerpt_more( $more ) {
	return '[.....]';
}
add_filter('excerpt_more', 'new_excerpt_more');

function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
/**
 * Print custom header styles
 * @return void
 */
function lmcn_custom_header() {
	$styles = '';
	if ( $color = get_header_textcolor() ) {
		echo '<style type="text/css"> ' .
				'.site-header .logo .blog-name, .site-header .logo .blog-description {' .
					'color: #' . $color . ';' .
				'}' .
			 '</style>';
		echo "<link href='http://fonts.googleapis.com/css?family=Prata' rel='stylesheet' type='text/css'>";
	}
}
add_action( 'wp_head', 'lmcn_custom_header', 11 );

$custom_bg_args = array(
	'default-color' => 'fba919',
	'default-image' => '',
);
add_theme_support( 'custom-background', $custom_bg_args );

register_nav_menu( 'main-menu', __( 'Your sites main menu', 'lmcn' ) );

if ( function_exists( 'register_sidebars' ) ) {
	register_sidebar(
		array(
			'id' => 'home-sidebar',
			'name' => __( 'Home widgets', 'lmcn' ),
			'description' => __( 'Shows on home page', 'lmcn' )
		)
	);

	register_sidebar(
		array(
			'id' => 'footer-sidebar',
			'name' => __( 'Footer widgets', 'lmcn' ),
			'description' => __( 'Shows in the sites footer', 'lmcn' )
		)
	);
}

if ( ! isset( $content_width ) ) $content_width = 650;

/**
 * Include editor stylesheets
 * @return void
 */
function lmcn_editor_style() {
    add_editor_style( 'css/wp-editor-style.css' );
}
add_action( 'init', 'lmcn_editor_style' );


/******************************************************************************\
	Scripts and Styles
\******************************************************************************/

/**
 * Enqueue lmcn scripts
 * @return void
 */
function lmcn_enqueue_scripts() {
	wp_enqueue_style( 'lmcn-styles', get_stylesheet_uri(), array(), '1.0' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'default-scripts', get_template_directory_uri() . '/js/scripts.min.js', array(), '1.0', true );
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'lmcn_enqueue_scripts' );


/******************************************************************************\
	Content functions
\******************************************************************************/

/**
 * Displays meta information for a post
 * @return void
 */
function lmcn_post_meta() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			__( 'Posted %s in %s%s by %s. ', 'lmcn' ),
			get_the_time( get_option( 'date_format' ) ),
			get_the_category_list( ', ' ),
			get_the_tag_list( __( ', <b>Tags</b>: ', 'lmcn' ), ', ' ),
			get_the_author_link()
		);
	}
	edit_post_link( __( ' (edit)', 'lmcn' ), '<span class="edit-link">', '</span>' );
}
function lmcn_post_category() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			strtoupper(get_the_category_list( ', ' ))
		);
	}
}
function lmcn_post_date() {
	if ( get_post_type() == 'post' ) {
		echo sprintf(
			get_the_time( get_option( 'date_format' ) )
		);
	}
	edit_post_link( __( ' (edit)', 'lmcn' ), '<span class="edit-link">', '</span>' );
}