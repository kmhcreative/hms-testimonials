<?php
/*
Plugin Name: HMS Testimonials
Plugin URI: http://hitmyserver.net
Description: Displays your customer testimonials or rotate them. Utilize templates to customize how they are shown.
Version: 2.2.30
Author: HitMyServer LLC
Author URI: http://hitmyserver.net
*/


define('HMS_TESTIMONIALS', plugin_dir_path(__FILE__));
require_once HMS_TESTIMONIALS . 'setup.php';
require_once HMS_TESTIMONIALS . 'shortcodes.php';
require_once HMS_TESTIMONIALS . 'widgets.php';
require_once HMS_TESTIMONIALS . 'admin.php';

/**
 * What database version of the plugin are we on
 **/
$hms_testimonials_db_version = 15;
$hms_shown_rating_aggregate = false;
$hms_testimonial_footer_rating_aggregate = '';

add_action('wp_enqueue_scripts', function() {         wp_enqueue_script('hms_testimonials-rotator',   plugins_url('rotator.js',__FILE__), array('jquery') ); } );
add_action('plugins_loaded', 'hms_testimonials_db_check');
add_action('init', 'hms_testimonials_form_submission');
add_action('admin_init', function(){ HMS_Testimonials::getInstance(); });
add_action('admin_menu', function(){ HMS_Testimonials::getInstance()->admin_menus();});
add_action('admin_head', function(){ HMS_Testimonials::getInstance()->admin_head();});
add_action('wp_footer',  function(){ global $hms_testimonial_footer_rating_aggregate; echo $hms_testimonial_footer_rating_aggregate; });
add_action('widgets_init', 'hms_testimonials_widgets');

add_shortcode('hms_testimonials', 'hms_testimonials_show');
add_shortcode('hms_testimonials_rotating', 'hms_testimonials_show_rotating');
add_shortcode('hms_testimonials_form', 'hms_testimonials_form');

add_filter('plugin_action_links', array('HMS_Testimonials', 'settings_link'), 10, 2);

if ( !is_admin() ) {
	$settings = get_option('hms_testimonials');
	if (isset($settings['show_fancy_stars']) && $settings['show_fancy_stars'] == 1) {
		function hms_rating_override($text) {
			/* Detect the current rating */
			$matches = null;
			$getMatches = preg_match('/data-rating=\"(\d)\"/', $text, $matches);

			if ( count($matches) == 2 ) {
				$rating = $matches[1];
				return '<div class="hms-stars rating-'. $rating .'" itemprop="ratingValue"><span>'. $rating .' out of 5 stars</span></div>';
			}

			return $text;
		}
		add_filter('hms_testimonials_system_rating', 'hms_rating_override');

		function hms_testimonials_styles(){
	//		if ( is_front_page() || is_home()) {
				wp_enqueue_style( 'hms-testimonials', plugin_dir_url(__FILE__) . 'hms-styles.css' );
	//		}
		}
		add_action( 'wp_enqueue_scripts', 'hms_testimonials_styles', 999);
	}
}