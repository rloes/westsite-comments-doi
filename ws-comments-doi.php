<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/rloes
 * @since             1.0.0
 * @package           Ws_Comments_Doi
 *
 * @wordpress-plugin
 * Plugin Name:       Double Opt-in for Comments
 * Plugin URI:        https://westsite-webdesign.de
 * Description:       Only receive comments from real email addresses. The first comment of an email address will only be posted after a link received by email was clicked.
 * Version:           1.0.0
 * Author:            Robin | Westsite
 * Author URI:        https://github.com/rloes/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ws-comments-doi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WS_COMMENTS_DOI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ws-comments-doi-activator.php
 */
function activate_ws_comments_doi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ws-comments-doi-activator.php';
	Ws_Comments_Doi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ws-comments-doi-deactivator.php
 */
function deactivate_ws_comments_doi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ws-comments-doi-deactivator.php';
	Ws_Comments_Doi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ws_comments_doi' );
register_deactivation_hook( __FILE__, 'deactivate_ws_comments_doi' );

// Define the function that will add the settings link
function ws_comment_doi_add_settings_link( $links ) {
	$settings_link = '<a href="' . admin_url( 'options-discussion.php#doi_section' ) . '">' . __( 'Settings', 'default' ) . '</a>';
	array_push( $links, $settings_link );

	return $links;
}

// Add the settings link to the plugins page
$plugin_basename = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin_basename", 'ws_comment_doi_add_settings_link' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ws-comments-doi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ws_comments_doi() {
	$enable_comment_doi = get_option('ws_comments_doi_email_checkbox', false);
	if($enable_comment_doi) {
		$plugin = new Ws_Comments_Doi();
		$plugin->run();
	}

}

run_ws_comments_doi();
