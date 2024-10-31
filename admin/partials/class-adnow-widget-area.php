<?php
/**
 * Adnow Widget Area class
 *
 * @file
 * @package Adnow Widget
 * @phpcs:disable WordPress.DB.DirectDatabaseQuery
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions
 */

/**
 * Adnow Widget Area class
 */
class Adnow_Widget_Area {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$edit_area = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", 'edit_area' ) );
		if ( ! isset( $edit_area ) ) {
			$inc = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options ( option_name, option_value, autoload ) VALUES ( %s, %s, %s )", 'edit_area', 'yes', 'no' ) );
		}
	}
}

$token = get_option( $this->option_name . '_key' );
if ( ! empty( $token ) ) {
	$aadb_getter = new Adnow_Widget_Aadb_Getter( AWP_API_URL );
	$widgets     = $aadb_getter->get( $token );
} else {
	$widgets['validate'] = false;
}

if ( false !== $widgets['validate'] ) {
	$edit_area = new Adnow_Widget_Area();
	$url       = ! empty( $_GET['url'] ) ? sanitize_url( wp_unslash( $_GET['url'] ) ) : home_url();
} else {
	$url = admin_url() . 'admin.php?page=adnow-widget';
}

global $cache_page_secret;
if ( ! empty( $cache_page_secret ) ) {
	$url = add_query_arg( 'donotcachepage', $cache_page_secret, $url );
}

$src  = '<scr';
$src .= 'ipt>document.location.href="' . esc_html( $url ) . '"</';
$src .= 'scr';
$src .= 'ipt>';
echo do_shortcode( $src );
exit;
