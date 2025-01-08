<?php
/*
 * Plugin Name: ACF Galerie 4
 * Plugin URI: https://navz.me
 * Description: Enhance your WordPress website with ACF Galerie 4, a powerful and customizable gallery plugin.
 * Author: Navneil Naicker
 * Author URI: https://navz.me/
 * Text Domain: acf-galerie-4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version: 1.4.0
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ACFG4_VERSION', '1.4.0' );
define( 'ACFG4_PLUGIN', __FILE__ );
define( 'ACFG4_PLUGIN_BASENAME', plugin_basename( ACFG4_PLUGIN ) );
define( 'ACFG4_PLUGIN_NAME', trim( dirname( ACFG4_PLUGIN_BASENAME ), '/' ) );
define( 'ACFG4_PLUGIN_DIR', untrailingslashit( dirname( ACFG4_PLUGIN ) ) );

/**
 * Loads the text domain for translation in the plugin.
 *
 * Hooks into the `init` action to:
 * - Load the plugin's text domain for translation, allowing the plugin to support
 *   multiple languages.
 * - Specifies the path to the translation files, located in the `lang` directory.
 * - The `acf-galerie-4` text domain is used for translation strings within the plugin.
 *
 * This ensures that any text strings in the plugin can be translated according
 * to the user's WordPress language settings.
 *
 * @return void
 */
add_action('init', 'acfg4_textdomain');
function acfg4_textdomain() {
	load_plugin_textdomain( 'acf-galerie-4', false, basename( dirname( __FILE__ ) ) . '/lang' );
}

/**
 * Registers the custom ACF field type during WordPress initialization.
 *
 * Hooks into the `init` action to:
 * - Check if the `acf_register_field_type` function is available. 
 *   This ensures compatibility with ACF Pro 5.0 or higher.
 * - Require the file that defines the `acfg4_register_field_type` class.
 * - Register the custom field type (`acfg4_register_field_type`) with ACF.
 *
 * This setup ensures that the `galerie-4` custom field type is properly
 * initialized and available for use in ACF field groups.
 *
 * @return void
 */
add_action( 'init', 'acfg4_init_register_type' );
function acfg4_init_register_type() {
	if ( ! function_exists( 'acf_register_field_type' ) ) return;
	require_once __DIR__ . '/providers/class.register-field-type.php';
	acf_register_field_type( 'acfg4_register_field_type' );
}

/**
 * Registers the custom ACF field type with WPGraphQL.
 *
 * Hooks into the `wpgraphql/acf/registry_init` action to initialize
 * support for the custom `galerie-4` ACF field type in WPGraphQL.
 *
 * The function:
 * - Requires the WPGraphQL integration class file.
 * - Instantiates the `acfg4_wpgraphql` class to register and configure 
 *   the field type within the WPGraphQL schema.
 */
add_action( 'wpgraphql/acf/registry_init', function() {
	require_once __DIR__ . '/providers/class.wpgraphql.php';
	new acfg4_wpgraphql();
});

add_action( 'init', 'acfg4_migration' );
function acfg4_migration() {
    require_once __DIR__ . '/providers/class.migration.php';
    new acfg4_migration();
}