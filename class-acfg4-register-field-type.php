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
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 *
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'galerie-4';

		/**
		 * Field type label.
		 *
		 * For public-facing UI. May contain spaces.
		 */
		$this->label = __( 'Galerie 4', 'acf-galerie-4' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'content'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

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
			$attachments = $this->get_attachments( $attachment_ids );
		}
	?>
		<div>
			<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="" />
			<input type="hidden" name="_<?php echo esc_attr( $this->name ); ?>_nonce[<?php echo esc_attr( $field['key'] ); ?>]" value="<?php echo esc_attr( wp_create_nonce( esc_attr( $field['key'] ) ) ); ?>" />
			<div class="<?php echo esc_attr( $this->add_class('container') ); ?>">
				<div class="<?php echo esc_attr( $this->add_class('attachments') ); ?> <?php echo esc_attr( $this->add_class('attachments') ); ?>-<?php echo esc_attr( $field['key'] ); ?>">
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
								<button
									type="button"
									class="<?php echo esc_attr( $this->add_class('remove-attachment') ); ?>"
									title="Remove this media">
									<span class="dashicons dashicons-trash"></span>
								</button>
								<input type="hidden" name="<?php echo esc_attr( $field['type'] ); ?>[<?php echo esc_attr( $field['_name'] ); ?>][]" value="<?php echo esc_attr( $attachment_id ); ?>" />
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

	function update_value( $value, $post_id, $field ) {
		$field_type = $field['type'];
		$field_key = $field['key'];
		$nonce_key = "_{$field_type}_nonce";

		if(
			empty( $_POST[$nonce_key] ) or 
			empty( $_POST[$nonce_key][$field_key] ) ) die();

		$nonce_key = sanitize_text_field( wp_unslash( $_POST[$nonce_key][$field_key] ) );
		
		if( ! wp_verify_nonce( $nonce_key, $field_key ) ) die();

		if ( empty( $_POST[$field_type] ) or empty( $_POST[$field_type][$field['name']] ) ){
			return array();
		}
		
		$value = array_map( 'sanitize_text_field', wp_unslash( $_POST[$field_type][$field['name']] ) );

		return array_map( 'intval', $value );
	}

	function format_value( $value, $post_id, $field ) {
		if ( empty($value) ) die();

		$attachment_ids = array_map( 'intval', $value );

		return $this->get_attachments( $attachment_ids );
	}

	function get_attachments( $attachment_ids ){
		$attachments = get_posts(
			array(
				'post_type' => 'attachment',
				'post__in' => $attachment_ids,
				'post_status' => 'inherit',
				'orderby' => 'post__in',
				'order' => 'ASC',
				'numberposts' => -1
			)
		);

		$attachment_data = array();

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
	
	function add_class($class){
		return esc_attr("acf-galerie-4-{$class}");
	}

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

		if( is_admin() && 'term.php' == basename($_SERVER["SCRIPT_NAME"]) ){
			wp_enqueue_media();
		}
	}
}
