<?php
/**
 * Plugin Name: Nimble Dashboard Instructions
 * Plugin URI:  https://nimble.help/
 * GitHub Plugin URI: https://github.com/Ljuk67/Nimble-Dashboard-instructions
 * Primary Branch: master
 * Description: Displays reusable Nimble.Help dashboard instructions and image-optimization reminders in the media library and upload modal.
 * Version:     1.0.1
 * Author:      Nimble.Help
 * Author URI:  https://nimble.help/
 * Text Domain: nimble-dashboard-instructions
 * Domain Path: /languages
 *
 * @package NimbleDashboardInstructions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-nimble-dashboard-instructions.php';

/**
 * Boot the plugin.
 */
function nimble_dashboard_instructions() {
	static $plugin = null;

	if ( null === $plugin ) {
		$plugin = new Nimble_Dashboard_Instructions();
	}

	return $plugin;
}

nimble_dashboard_instructions();
