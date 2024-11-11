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

		add_action( 'wpgraphql/acf/registry_init', array($this, 'register_graphql') );
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
		$attachment_ids = array_map( 'intval', $value );

		if( empty( $attachment_ids ) ){
			return array();
		}

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

		// register & include JS
		wp_enqueue_script('jquery-ui-sortable');

		if( is_admin() && in_array(basename($_SERVER["SCRIPT_NAME"]), array('profile.php', 'term.php', 'edit-tags.php'))){
			wp_enqueue_media();
		}
	}

	public function acfg4_get_gallery( $attachment_ids ){
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
	
		$graphql_format = [];
	
		foreach( $attachments as $attachment ){
			$l_graphql_format = [];
			$l_graphql_format = [
				'id' => $attachment->ID,
				'postAuthor' => $attachment->post_author,
				'postDate' => $attachment->post_date,
				'postDateGmt' => $attachment->post_date_gmt,
				'postContent' => $attachment->post_content,
				'postTitle' => $attachment->post_title,
				'postExcerpt' => $attachment->post_excerpt,
				'postStatus' => $attachment->post_status,
				'commentStatus' => $attachment->comment_status,
				'pingStatus' => $attachment->ping_status,
				'postName' => $attachment->post_name,
				'toPing' => $attachment->to_ping,
				'pinged' => $attachment->pinged,
				'postModified' => $attachment->post_modified,
				'postModifiedGmt' => $attachment->post_modified_gmt,
				'postContentFiltered' => $attachment->post_content_filtered,
				'postParent' => $attachment->post_parent,
				'menuOrder' => $attachment->menu_order,
				'postType' => $attachment->post_type,
				'postMimeType' => $attachment->post_mime_type,
				'commentCount' => $attachment->comment_count,
				'filter' => $attachment->filter
			];
	
			if ( !preg_match('/image\/\w+/', $attachment->post_mime_type ) ) {
				$file_url = wp_get_attachment_url( $attachment->ID );
				array_push($l_graphql_format, ['file' => basename( $file_url )]);
				array_push($l_graphql_format, ['mimeType' => $attachment->post_mime_type]);
				array_push($l_graphql_format, ['fileSize' => $md['filesize'] ?? ""]);
				array_push($l_graphql_format, ['fileUrl' => $file_url ?? ""]);
			} else {
				$md = wp_get_attachment_metadata( $attachment->ID );
	
				$l_graphql_format['fullFile'] = $md['file'] ?? "";
				$l_graphql_format['fullWidth'] = $md['width'] ?? "";
				$l_graphql_format['fullHeight'] = $md['height'] ?? "";
				$l_graphql_format['fullMimeType'] = $attachment->post_mime_type;
				$l_graphql_format['fullFileSize'] = $md['filesize'];
				$l_graphql_format['fullFileUrl'] = wp_get_attachment_image_src( $attachment->ID, 'full' )[0];
	
				if( !empty( $md['sizes'] ) ){
					foreach( $md['sizes'] as $key => $value ){
						if( in_array($key, ['medium', 'large', 'thumbnail', 'medium_large']) ){
							$file_url = wp_get_attachment_image_src( $attachment->ID, $key );
							$key = str_replace( '-', '_', $key );
							$key = str_replace( '_', '', $key );
							$key = str_replace( 'filesize', 'fileSize', $key );
							$value['fileUrl'] = !empty($file_url && $file_url[0]) ? $file_url[0] : "";
		
							$l_graphql_format[$key . 'FullFile'] = $value['fileUrl'];
							$l_graphql_format[$key . 'fullWidth'] = $value['width'] ?? "";
							$l_graphql_format[$key . 'fullHeight'] = $value['height'] ?? "";
							$l_graphql_format[$key . 'fullMimeType'] = $value['mime-type'];
							$l_graphql_format[$key . 'fullFileSize'] = $value['filesize'];
							$l_graphql_format[$key . 'fullFileUrl'] = $value['fileUrl'];	
						}
					}
				}
			}
	
			array_push($graphql_format, $l_graphql_format);
		}
	
		return $graphql_format;
	}

	public function register_graphql(){
		register_graphql_object_type(
			'ACF_Galerie_4',
			[
				'description' => __( 'Registered image size', 'acf-galerie-4' ),
				'fields'      => [
					'id' => [
						'type'        => 'String',
						'description' => __( 'Attachment ID.', 'acf-galerie-4' ),
					],
					'postAuthor' => [
						'type'        => 'Integer',
						'description' => __( 'Post author ID.', 'acf-galerie-4' ),
					],
					'postDate' => [
						'type'        => 'String',
						'description' => __( 'Post date.', 'acf-galerie-4' ),
					],
					'postDateGmt' => [
						'type'        => 'String',
						'description' => __( 'Post date in GMT.', 'acf-galerie-4' ),
					],
					'postContent' => [
						'type'        => 'String',
						'description' => __( 'Post content.', 'acf-galerie-4' ),
					],
					'postTitle' => [
						'type'        => 'String',
						'description' => __( 'Post title.', 'acf-galerie-4' ),
					],
					'postExcerpt' => [
						'type'        => 'String',
						'description' => __( 'Post excerpt.', 'acf-galerie-4' ),
					],
					'postStatus' => [
						'type'        => 'String',
						'description' => __( 'Post status.', 'acf-galerie-4' ),
					],
					'commentStatus' => [
						'type'        => 'String',
						'description' => __( 'Comment status.', 'acf-galerie-4' ),
					],
					'pingStatus' => [
						'type'        => 'String',
						'description' => __( 'Ping status.', 'acf-galerie-4' ),
					],
					'postName' => [
						'type'        => 'String',
						'description' => __( 'Post name.', 'acf-galerie-4' ),
					],
					'toPing' => [
						'type'        => 'String',
						'description' => __( 'To ping list.', 'acf-galerie-4' ),
					],
					'pinged' => [
						'type'        => 'String',
						'description' => __( 'Pinged list.', 'acf-galerie-4' ),
					],
					'postModified' => [
						'type'        => 'String',
						'description' => __( 'Post modified date.', 'acf-galerie-4' ),
					],
					'postModifiedGmt' => [
						'type'        => 'String',
						'description' => __( 'Post modified date in GMT.', 'acf-galerie-4' ),
					],
					'postContentFiltered' => [
						'type'        => 'String',
						'description' => __( 'Filtered post content.', 'acf-galerie-4' ),
					],
					'postParent' => [
						'type'        => 'Integer',
						'description' => __( 'Post parent ID.', 'acf-galerie-4' ),
					],
					'menuOrder' => [
						'type'        => 'Integer',
						'description' => __( 'Menu order.', 'acf-galerie-4' ),
					],
					'postType' => [
						'type'        => 'String',
						'description' => __( 'Post type.', 'acf-galerie-4' ),
					],
					'postMimeType' => [
						'type'        => 'String',
						'description' => __( 'Post mime type.', 'acf-galerie-4' ),
					],
					'commentCount' => [
						'type'        => 'Integer',
						'description' => __( 'Comment count.', 'acf-galerie-4' ),
					],
					'filter' => [
						'type'        => 'String',
						'description' => __( 'Post filter.', 'acf-galerie-4' ),
					],
					'fullFile' => [
						'type'        => 'String',
						'description' => __( 'Full file name.', 'acf-galerie-4' ),
					],
					'fullWidth' => [
						'type'        => 'Integer',
						'description' => __( 'Full file width.', 'acf-galerie-4' ),
					],
					'fullHeight' => [
						'type'        => 'Integer',
						'description' => __( 'Full file height.', 'acf-galerie-4' ),
					],
					'fullMimeType' => [
						'type'        => 'String',
						'description' => __( 'Full file mime type.', 'acf-galerie-4' ),
					],
					'fullFileSize' => [
						'type'        => 'Integer',
						'description' => __( 'Full file size in bytes.', 'acf-galerie-4' ),
					],
					'fullFileUrl' => [
						'type'        => 'String',
						'description' => __( 'Full file URL.', 'acf-galerie-4' ),
					],
					'mediumFullFile' => [
						'type'        => 'String',
						'description' => __( 'Medium file URL.', 'acf-galerie-4' ),
					],
					'mediumfullWidth' => [
						'type'        => 'Integer',
						'description' => __( 'Medium file width.', 'acf-galerie-4' ),
					],
					'mediumfullHeight' => [
						'type'        => 'Integer',
						'description' => __( 'Medium file height.', 'acf-galerie-4' ),
					],
					'mediumfullMimeType' => [
						'type'        => 'String',
						'description' => __( 'Medium file mime type.', 'acf-galerie-4' ),
					],
					'mediumfullFileSize' => [
						'type'        => 'Integer',
						'description' => __( 'Medium file size in bytes.', 'acf-galerie-4' ),
					],
					'mediumfullFileUrl' => [
						'type'        => 'String',
						'description' => __( 'Medium file URL.', 'acf-galerie-4' ),
					],
					'largeFullFile' => [
						'type'        => 'String',
						'description' => __( 'Large file URL.', 'acf-galerie-4' ),
					],
					'largefullWidth' => [
						'type'        => 'Integer',
						'description' => __( 'Large file width.', 'acf-galerie-4' ),
					],
					'largefullHeight' => [
						'type'        => 'Integer',
						'description' => __( 'Large file height.', 'acf-galerie-4' ),
					],
					'largefullMimeType' => [
						'type'        => 'String',
						'description' => __( 'Large file mime type.', 'acf-galerie-4' ),
					],
					'largefullFileSize' => [
						'type'        => 'Integer',
						'description' => __( 'Large file size in bytes.', 'acf-galerie-4' ),
					],
					'largefullFileUrl' => [
						'type'        => 'String',
						'description' => __( 'Large file URL.', 'acf-galerie-4' ),
					],
					'thumbnailFullFile' => [
						'type'        => 'String',
						'description' => __( 'Thumbnail file URL.', 'acf-galerie-4' ),
					],
					'thumbnailfullWidth' => [
						'type'        => 'Integer',
						'description' => __( 'Thumbnail file width.', 'acf-galerie-4' ),
					],
					'thumbnailfullHeight' => [
						'type'        => 'Integer',
						'description' => __( 'Thumbnail file height.', 'acf-galerie-4' ),
					],
					'thumbnailfullMimeType' => [
						'type'        => 'String',
						'description' => __( 'Thumbnail file mime type.', 'acf-galerie-4' ),
					],
					'thumbnailfullFileSize' => [
						'type'        => 'Integer',
						'description' => __( 'Thumbnail file size in bytes.', 'acf-galerie-4' ),
					],
					'thumbnailfullFileUrl' => [
						'type'        => 'String',
						'description' => __( 'Thumbnail file URL.', 'acf-galerie-4' ),
					],
					'mediumlargeFullFile' => [
						'type'        => 'String',
						'description' => __( 'Medium large file URL.', 'acf-galerie-4' ),
					],
					'mediumlargefullWidth' => [
						'type'        => 'Integer',
						'description' => __( 'Medium large file width.', 'acf-galerie-4' ),
					],
					'mediumlargefullHeight' => [
						'type'        => 'Integer',
						'description' => __( 'Medium large file height.', 'acf-galerie-4' ),
					],
					'mediumlargefullMimeType' => [
						'type'        => 'String',
						'description' => __( 'Medium large file mime type.', 'acf-galerie-4' ),
					],
					'mediumlargefullFileSize' => [
						'type'        => 'Integer',
						'description' => __( 'Medium large file size in bytes.', 'acf-galerie-4' ),
					],
					'mediumlargefullFileUrl' => [
						'type'        => 'String',
						'description' => __( 'Medium large file URL.', 'acf-galerie-4' ),
					],
				],
			]
		);
	
		register_graphql_acf_field_type(
			'galerie-4',
			[
				'exclude_admin_fields' => [ 'graphql_non_null' ],
				'graphql_type' => [ 'list_of' => 'ACF_Galerie_4' ],
				'resolve' => static function ( $root, $args, $context, $info, $field_type, $field_config ) {
					$value = $field_config->resolve_field( $root, $args, $context, $info );
	
					if ( empty( $value ) ) {
						return null;
					}
	
					$value = array_filter( $value );
	
					return array_filter( $this->acfg4_get_gallery( $value ) );
				},
			]
		);
	}
}
