<?php
/**
 * Defines the custom field type class.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class acfg4_migration
{
    public function __construct()
    {
        add_action('admin_head', array($this, 'acfg4_start_migration_nonce'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_plugin_admin_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_plugin_admin_styles'));
        add_action('wp_ajax_acfg4_start_migration', array($this, 'acfg4_start_migration'));
    }
    
    function acfg4_start_migration_nonce() {
        if ( !is_admin() ) return;
        $nonce = wp_create_nonce('acfg4_start_migration_nonce');
    ?>
        <script type="text/javascript">const acfg4_start_migration_nonce = "<?php echo $nonce; ?>";</script>
    <?php
    }

    public function enqueue_plugin_admin_scripts() {
        wp_enqueue_script('acfg4-admin-script', ACFG4_PLUGIN_URL . 'assets/js/admin-script.js', ['jquery'], '1.0.0', true);
    }

    public function enqueue_plugin_admin_styles() {
        wp_enqueue_style('acfg4-admin-css', ACFG4_PLUGIN_URL . 'assets/css/admin-style.css', [], '1.0.0');
    }

    public function acfg4_start_migration() {
        global $wpdb;
        $wpdb->query('START TRANSACTION');

        try {
            $migrate_from = $_POST['migrate_from'];

            if (
                isset( $_POST['nonce'] ) &&
                !wp_verify_nonce( $_POST['nonce'], 'acfg4_start_migration_nonce') )
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
                    if( $migrate_from == 1 ){
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
}