<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/rloes
 * @since      1.0.0
 *
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ws_Comments_Doi
 * @subpackage Ws_Comments_Doi/includes
 * @author     Robin | Westsite <loeseke@westsite-webdesign.de>
 */
class Ws_Comments_Doi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		update_option('ws_comments_doi_email_checkbox', true);
	}

}
