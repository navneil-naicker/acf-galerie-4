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
 * Version: 1.0.0
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ACFG4_VERSION', '1.0.0' );

define( 'ACFG4_PLUGIN', __FILE__ );

define( 'ACFG4_PLUGIN_BASENAME', plugin_basename( ACFG4_PLUGIN ) );

define( 'ACFG4_PLUGIN_NAME', trim( dirname( ACFG4_PLUGIN_BASENAME ), '/' ) );

define( 'ACFG4_PLUGIN_DIR', untrailingslashit( dirname( ACFG4_PLUGIN ) ) );

/**
 * Registration logic for the new ACF field type.
 */

add_action( 'init', 'acfg4_init_register_type' );

/**
 * Registers the ACF field type.
 */
function acfg4_init_register_type() {
	if ( ! function_exists( 'acf_register_field_type' ) ) return;

	require_once __DIR__ . '/class-acfg4-register-field-type.php';

	acf_register_field_type( 'acfg4_register_field_type' );
}
