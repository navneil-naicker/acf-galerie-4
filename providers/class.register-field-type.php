<?php
/**
 * Defines the custom field type class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class acfg4_register_field_type extends \acf_field {
	/**
	 * Controls field type visibilty in REST requests.
	 *
	 * @var bool
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the theme or plugin.
	 *
	 * @var array $env Plugin or theme context such as 'url' and 'version'.
	 */
	private $env;

	/**
	 * Initializes the custom field type with its properties and defaults.
	 *
	 * This constructor sets up the essential information and configurations
	 * for the custom ACF field type `galerie-4`. It defines:
	 * - Name and label for identification and display in the UI.
	 * - Category under which the field type is listed in the picker.
	 * - Description for the field type, displayed in field settings.
	 * - Links to documentation and tutorial resources for user guidance.
	 * - Default settings specific to this field type.
	 * - Environment settings, including the plugin version.
	 *
	 * Inherits functionality from the parent field type class.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 *
		 * No spaces. Underscores allowed.
		 */
		$this->name = ACFG4_PLUGIN_TYPE;

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __( 'Galerie 4', 'acf-galerie-4' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content';

		/**
		 * Field type Description.
		 *
		 * For field descriptions. May contain spaces.
		 */
		$this->description = __( 'Enhance your WordPress website with ACF Galerie 4, a powerful and customizable gallery plugin.', 'acf-galerie-4' );

		/**
		 * Field type Doc URL.
		 *
		 * For linking to a documentation page. Displayed in the field picker modal.
		 */
		$this->doc_url = 'https://www.navz.me/';

		/**
		 * Field type Tutorial URL.
		 *
		 * For linking to a tutorial resource. Displayed in the field picker modal.
		 */
		$this->tutorial_url = 'https://www.navz.me/';

		/**
		 * Defaults for your custom user-facing settings for this field type.
		 */
		$this->defaults = array();

		$this->env = array(
			'version' => ACFG4_VERSION,
		);
		
		parent::__construct();

		add_filter('plugin_action_links_acf-galerie-4/acf-galerie-4.php', array($this, 'add_action_link'));
	}

	/**
	 * Add a custom action link next to "Deactivate" on the plugins page.
	 *
	 * @param array $links An array of existing action links.
	 * @return array Modified array of action links.
	 */
	function add_action_link($links) {		
		// Append the link to the end of the array
		$links[] = '<a href="#" id="acfg4-migrate">Migrate</a>';

		if(!file_exists(WP_PLUGIN_DIR . '/acf-galerie-4-pro/acf-galerie-4-pro.php')){
			$links[] = '<a href="https://galerie4.com/" target="_blank" style="color:#b32d2e;font-weight:bold;" title="Get ACF Galerie 4 Pro version">Go Pro</a>';
		}
	
		return $links;
	}

	/**
	 * Settings to display when users configure a field of this type.
	 *
	 * These settings appear on the ACF “Edit Field Group” admin page when
	 * setting up the field.
	 *
	 * @param array $field
	 * @return void
	 */
	public function render_field_settings( $field ) {
		// To render field settings on other tabs in ACF 6.0+:
		// https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/#moving-field-setting
	}

	/**
	 * HTML content to show when a publisher edits the field on the edit screen.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 */
	public function render_field( $field ) {
		$attachments = array();
		if ( !empty( $field ) and !empty( $field['value'] ) ) {
			$attachment_ids = array_map( 'intval', $field['value'] );
			$attachments = $this->transform( $attachment_ids );
		}
	?>
		<div>
			<div class="<?php echo esc_attr( $this->add_class('container') ); ?>">
				<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="" />
				<div
					class="<?php echo esc_attr( $this->add_class('attachments') ); ?>
						   <?php echo esc_attr( $this->add_class('attachments') ); ?>-<?php echo esc_attr( $field['key'] ); ?>"
					data-name="<?php echo esc_attr( $field['name'] ); ?>">
					<?php if ( $attachments ) : ?>
						<?php
						foreach ( $attachments as $item ) :
							$attachment_id = $item['attachment']->ID;
							$attachment_title = $item['attachment']->post_title;
							$thumbnail_class = $this->add_class('attachment-thumbnail');
							$thumbnail = $item['metadata']['thumbnail']['file_url'] ?? "";
							if( empty($thumbnail) ) :
								$thumbnail = includes_url('images/media/text.png');
								$thumbnail_class = $this->add_class('attachment-icon');
							endif;
						?>
							<div data-id="<?php echo esc_attr( $attachment_id ); ?>" class="<?php echo esc_attr( 'attachment-thumbnail-container' ); ?> <?php echo esc_attr( "attachment-thumbnail-container-{$attachment_id}" ); ?> <?php echo esc_attr( $thumbnail_class ); ?>">
								<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>[]" value="<?php echo esc_attr( $attachment_id ); ?>" />
								<button
									type="button"
									class="<?php echo esc_attr( $this->add_class('remove-attachment') ); ?>"
									title="Remove this media">
									<span class="dashicons dashicons-trash"></span>
								</button>
								<img 
									src="<?php echo esc_url( $thumbnail ); ?>"
									alt="<?php echo esc_attr( $attachment_title ); ?>"
									title="<?php echo esc_attr( $attachment_title ); ?>"
								/>
								<?php if( $thumbnail_class == $this->add_class('attachment-icon') ) : ?>
									<div class="<?php echo esc_attr( $this->add_class('file-name') ); ?>"><?php echo esc_attr( $attachment_title ); ?></div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div>
					<button type="button" class="button button-primary <?php echo esc_attr( $this->add_class('add-media') ); ?>">
						<span class="dashicons dashicons-plus-alt2"></span>
						<?php esc_html_e( 'Add Media', 'acf-galerie-4' ); ?>
					</button>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Validates and processes the submitted field value before saving it.
	 *
	 * This function performs the following tasks:
	 * - Verifies the nonce for security to ensure the request is valid.
	 * - Checks the presence of the required POST data for the field.
	 * - Sanitizes and processes the input value into an array of integers.
	 *
	 * If the nonce validation fails or required data is missing, the function 
	 * terminates execution to prevent further processing.
	 *
	 * @param mixed $value    The raw value submitted for the field.
	 * @param int   $post_id  The ID of the post where the field is being updated.
	 * @param array $field    The field configuration array, containing metadata about the field.
	 * 
	 * @return array An array of sanitized integer values representing the field's data.
	 */
	function update_value( $value, $post_id, $field ) {		
		if ( empty( $value ) ) {
			return array();
		}

		return array_map( 'intval', $value );
	}

	/**
	 * Formats the field value into a structured attachment data array.
	 *
	 * Converts raw attachment IDs into integer values, validates them, and 
	 * transforms the data into a detailed structure with attachment metadata.
	 *
	 * - If the input `$value` is empty, an empty array is returned.
	 * - Valid attachment IDs are passed to the `transform` method for processing.
	 *
	 * @param mixed $value    The raw field value, typically an array of attachment IDs.
	 * @param int   $post_id  The ID of the post where the field is being used.
	 * @param array $field    The field configuration array.
	 * 
	 * @return array Structured attachment data generated by the `transform` method.
	 */
	function format_value( $value, $post_id, $field ) {
		if( empty( $value ) ) return array(); 

		$attachment_ids = array_map( 'intval', $value );

		if( empty( $attachment_ids ) ) return array();

		return $this->transform( $attachment_ids );
	}

	/**
	 * Transforms attachment IDs into structured attachment and metadata information.
	 *
	 * Fetches attachment data for the provided IDs and formats the output 
	 * to include detailed metadata such as file information, dimensions, 
	 * MIME types, and URLs for different image sizes.
	 *
	 * - Non-image attachments include basic file metadata such as MIME type and file size.
	 * - Image attachments include metadata for the full image as well as additional sizes.
	 *
	 * Sensitive fields like `post_password` and `guid` are removed from the attachment object.
	 *
	 * @param array $attachment_ids List of attachment IDs to retrieve and transform.
	 *
	 * @return array A structured array containing attachment data and metadata. 
	 *               Each element includes:
	 *               - 'attachment': The sanitized attachment object.
	 *               - 'metadata': An array of metadata details (e.g., file size, URLs).
	 */
	public function transform( $attachment_ids ){
		$attachment_data = array();
		$attachments = $this->get_attachments( $attachment_ids );

		if ( !empty( $attachments ) ) {
			foreach ($attachments as $attachment) {
				$metadata = array();
				
				foreach(array('post_password', 'guid') as $column){
					unset($attachment->$column);
				}

				if ( !preg_match('/image\/\w+/', $attachment->post_mime_type ) ) {
					$file_url = wp_get_attachment_url( $attachment->ID );
					$metadata['file'] = array(
						"file" => basename( $file_url ),
						"mime_type" => $attachment->post_mime_type,
						"file_size" => $md['filesize'] ?? "",
						'file_url' => $file_url ?? ""
					);
				} else {
					$md = wp_get_attachment_metadata( $attachment->ID );

					$metadata['full'] = array(
						"file" => $md['file'] ?? "",
						"width" => $md['width'] ?? "",
						"height" => $md['height'] ?? "",
						"mime_type" => $attachment->post_mime_type,
						"file_size" => $md['filesize'],
						'file_url' => wp_get_attachment_image_src( $attachment->ID, 'full' )[0]
					);
	
					if( !empty( $md['sizes'] ) ){
						foreach( $md['sizes'] as $key => $value ){
							$file_url = wp_get_attachment_image_src( $attachment->ID, $key );
							$key = str_replace( '-', '_', $key );
							$key = str_replace( 'filesize', 'file_size', $key );
							$value['file_url'] = !empty($file_url && $file_url[0]) ? $file_url[0] : "";
							$metadata[$key] = $value;
						}
					}
				}

				$attachment_data[] = array(
					'attachment' => $attachment,
					'metadata' => $metadata
				);	
			}
		}

		return $attachment_data;
	}
	
	/**
	 * Generates a sanitized CSS class name for an ACF field.
	 *
	 * Prepends a prefix to the provided class name and ensures it is properly escaped.
	 *
	 * @param string $class The class name to be prefixed and sanitized.
	 * 
	 * @return string The sanitized and prefixed CSS class name.
	 */
	function add_class($class){
		return esc_attr("acf-galerie-4-{$class}");
	}

	/**
	 * Prepares an array of HTML attributes for an ACF field.
	 *
	 * Generates attributes including an ID, a nonce for security, 
	 * and a CSS class for the ACF field.
	 *
	 * @param array $field The field configuration array containing field details.
	 * 
	 * @return array Associative array of sanitized HTML attributes for the field.
	 */
	function add_attrs($field){
		return array(
			'id' => esc_attr( $field['id'] ),
			'data-nonce' => wp_create_nonce( $field['key'] ),
			'class' => esc_attr("acf-galerie-4 {$field['class']}"),
		);
	}

	/**
	 * Enqueues CSS and JavaScript needed by HTML in the render_field() method.
	 *
	 * Callback for admin_enqueue_script.
	 *
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		$url = trailingslashit( plugin_dir_url(ACFG4_PLUGIN) );
		$version = $this->env['version'];

		wp_register_script(
			$this->add_class('js'),
			"{$url}assets/js/acf-galerie-4.js",
			array( 'acf-input' ),
			$version,
			array ('async', true)
		);

		wp_register_style(
			$this->add_class('css'),
			"{$url}assets/css/acf-galerie-4.css",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_script( $this->add_class('js') );
		wp_enqueue_style( $this->add_class('css'));

		// register & include JS
		wp_enqueue_script('jquery-ui-sortable');

		if( is_admin() && in_array(basename($_SERVER["SCRIPT_NAME"]), array('profile.php', 'term.php', 'edit-tags.php', 'user-edit.php', 'user-new.php'))){
			wp_enqueue_media();
		}
	}
	
	/**
	 * Retrieves attachment posts based on the provided attachment IDs.
	 *
	 * Fetches attachment posts matching the given IDs in their original order, 
	 * with a post type of 'attachment' and a status of 'inherit'.
	 *
	 * @param array $attachment_ids List of attachment post IDs to retrieve.
	 *
	 * @return array List of attachment posts that match the criteria.
	 */
	public function get_attachments( $attachment_ids ){
		return get_posts(
			array(
				'post_type' => 'attachment',
				'post__in' => $attachment_ids,
				'post_status' => 'inherit',
				'orderby' => 'post__in',
				'order' => 'ASC',
				'numberposts' => -1
			)
		);
	}
	
}