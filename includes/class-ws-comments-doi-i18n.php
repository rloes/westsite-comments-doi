<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ws-comments-doi',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
