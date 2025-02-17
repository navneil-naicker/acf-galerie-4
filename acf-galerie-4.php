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
 * Version: 1.4.1
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'ACFG4' ) ) {

	class ACFG4 {

		public function initialize() {
			$this->define( 'ACFG4_VERSION', '1.4.1' );
			$this->define( 'ACFG4_PLUGIN', __FILE__ );
			$this->define( 'ACFG4_PLUGIN_BASENAME', plugin_basename( ACFG4_PLUGIN ) );
			$this->define( 'ACFG4_PLUGIN_NAME', trim( dirname( ACFG4_PLUGIN_BASENAME ), '/' ) );
			$this->define( 'ACFG4_PLUGIN_DIR', untrailingslashit( dirname( ACFG4_PLUGIN ) ) );
			$this->define( 'ACFG4_PLUGIN_URL', plugin_dir_url( ACFG4_PLUGIN ) );
			$this->define( 'ACFG4_PLUGIN_TYPE', 'galerie-4');

			add_action('init', array($this, 'init'));
			add_action( 'wpgraphql/acf/registry_init', array($this, 'wpgraphql'));
		}
		
		public function init() {
			// Load the text domain for translation
			$this->load_text_domain();

			// Include and instantiate the Migration class
			$this->initialize_migration();
		
			// Register custom ACF field type
			$this->register_acf_field_type();
		}

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
		public function load_text_domain() {
			load_plugin_textdomain( 'acf-galerie-4', false, basename( dirname( __FILE__ ) ) . '/lang' );
		}

		/**
		 * Includes and instantiates the Migration class to handle database migrations or other related tasks.
		 *
		 * @return void
		 */
		public function initialize_migration() {
			require_once __DIR__ . '/providers/class.migration.php';
			new acfg4_migration();
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
		public function register_acf_field_type() {
			if ( ! function_exists( 'acf_register_field_type' ) ) return;
			require_once __DIR__ . '/providers/class.register-field-type.php';
			acf_register_field_type( 'acfg4_register_field_type' );
		}

		public function wpgraphql(){
			require_once __DIR__ . '/providers/class.wpgraphql.php';
			new acfg4_wpgraphql();
		}

		/**
		 * Helper method to define constants.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}

	// Instantiate.
	$acfg4 = new ACFG4();
	$acfg4->initialize();
}