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
 * Version: 1.3.2
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ACFG4_VERSION', '1.3.2' );
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
	require_once __DIR__ . '/class-acfg4-register-field-type.php';
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

add_action('admin_head', 'acfg4_start_migration_nonce');
function acfg4_start_migration_nonce() {
    if ( !is_admin() ) return;
    $nonce = wp_create_nonce('acfg4_start_migration_nonce_action');
?>
    <script type="text/javascript">
        const acfg4_start_migration_nonce = "<?php echo $nonce; ?>";
    </script>
<?php
}

add_action('admin_enqueue_scripts', 'enqueue_plugin_admin_scripts');
function enqueue_plugin_admin_scripts() {
    wp_enqueue_script(
        'acfg4-admin-script',
        plugin_dir_url(__FILE__) . 'assets/js/admin-script.js',
        ['jquery'],
        '1.0.0',
        true
    );
}

add_action('admin_enqueue_scripts', 'enqueue_plugin_admin_styles');
function enqueue_plugin_admin_styles() {
	wp_enqueue_style(
		'acfg4-admin-css',
		plugin_dir_url(__FILE__) . 'assets/css/admin-style.css',
		[],
		'1.0.0'
	);
}

add_action('wp_ajax_acfg4_start_migration', 'acfg4_start_migration');
function acfg4_start_migration() {    
    global $wpdb;
    $wpdb->query('START TRANSACTION');

    try {
        $migrate_from = $_POST['migrate_from'];

        if (
            isset($_POST['nonce']) &&
            !check_admin_referer('acfg4_start_migration_nonce_action', 'acfg4_start_migration_nonce') )
        {
            wp_send_json_error(['message' => "Nonce verification failed. Please try again."], 400);
        }

        if( !in_array( $migrate_from, [1, 2] ) ){
            wp_send_json_error(['message' => "Choose which plugin you want to migrate from."], 400);
        }

        $fields = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_type = 'acf-field'");

        foreach( $fields as $field ){
            $field_name = $field->post_excerpt;
            $field_metadata = unserialize( $field->post_content );
            $field_type = $field_metadata['type'];

            if( in_array( $field_type, ['photo_gallery', 'gallery'])){
                $field_metadata['type'] = 'galerie-4'; 
                $updated_content = serialize($field_metadata);
                
                $wpdb->update(
                    $wpdb->posts,
                    array( 'post_content' => $updated_content ),
                    array( 'ID' => $field->ID )
                );

                //If ACF Photo Gallery Field, we want the ID's to be serialized.
                if( $migrate_from === 1 ){
                    $meta_fields = $wpdb->get_results(
                        $wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_key = %s", $field_name )
                    );
    
                    foreach( $meta_fields as $meta){
                        $meta_value = array_filter( explode(',', $meta->meta_value) );
                        $meta_value_serialized = serialize( $meta_value );
    
                        $wpdb->update(
                            $wpdb->postmeta,
                            array( 'meta_value' => $meta_value_serialized ),
                            array( 'meta_id' => $meta->meta_id )
                        );
                    }    
                }
            }
        }

        $wpdb->query('COMMIT');

        wp_send_json_success([
            'message' => 'Migration has successfully completed.'
        ]);
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => $e->getMessage()], 500);
    }

	die();
}
