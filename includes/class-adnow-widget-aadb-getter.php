<?php
/**
 * Adnow Widget Aadb Getter class
 *
 * @file
 * @package Adnow Widget
 * @phpcs:disable WordPress.DB.DirectDatabaseQuery
 * @phpcs:disable WordPress.PHP.DiscouragedPHPFunctions
 */

/**
 * Adnow Widget Aadb Getter class
 */
class Adnow_Widget_Aadb_Getter {

	/**
	 * Api url
	 *
	 * @var string $url
	 */
	private $url;


	/**
	 * Aadb getter constructor.
	 *
	 * @param string $api_url Api url.
	 */
	public function __construct( $api_url ) {
		$this->url = $api_url;
	}

	/**
	 * Getting aadb service response
	 *
	 * @param string $token Aadb token.
	 * @param bool   $validate Validate flag.
	 *
	 * @return mixed
	 */
	public function get( $token, $validate = true ) {
		$url  = $this->url . '?token=' . $token;
		$json = '';

		if ( $validate ) {
			$url .= '&validate=1';
		}

		$response = wp_remote_get( $url );
		if ( ! is_wp_error( $response ) ) {
			$json = wp_remote_retrieve_body( $response );
		}

		return json_decode( $json, true );
	}

}
