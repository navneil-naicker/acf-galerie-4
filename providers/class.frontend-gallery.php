<?php
/**
 * Registers frontend output for ACF Galerie 4 fields.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class acfg4_frontend_gallery {
	public function __construct() {
		add_shortcode( 'acf_galerie_4', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			'acfg4-frontend-gallery',
			ACFG4_PLUGIN_URL . 'assets/css/frontend-gallery.css',
			array(),
			ACFG4_VERSION
		);
	}

	/**
	 * Renders an ACF Galerie 4 field as a responsive gallery.
	 *
	 * @param array|string $attributes Shortcode attributes.
	 * @return string
	 */
	public function render_shortcode( $attributes ) {
		if ( ! function_exists( 'get_field_object' ) ) {
			return '';
		}

		$attributes = shortcode_atts(
			array(
				'field'   => '',
				'post_id' => get_the_ID(),
				'size'    => 'medium_large',
			),
			$attributes,
			'acf_galerie_4'
		);

		$field_name = sanitize_text_field( $attributes['field'] );
		$post_id    = absint( $attributes['post_id'] );
		$size       = sanitize_key( $attributes['size'] );

		if ( '' === $field_name || ! $post_id ) {
			return '';
		}

		$field = get_field_object( $field_name, $post_id, false, true );
		if ( ! is_array( $field ) || ACFG4_PLUGIN_TYPE !== ( $field['type'] ?? '' ) ) {
			return '';
		}

		$attachment_ids = $field['value'] ?? array();
		if ( ! is_array( $attachment_ids ) || empty( $attachment_ids ) ) {
			return '';
		}

		$items = '';
		foreach ( $attachment_ids as $attachment_id ) {
			$attachment_id = absint( $attachment_id );
			if ( ! $attachment_id || 'attachment' !== get_post_type( $attachment_id ) ) {
				continue;
			}

			$url = wp_get_attachment_url( $attachment_id );
			if ( ! $url ) {
				continue;
			}

			if ( wp_attachment_is_image( $attachment_id ) ) {
				$image = wp_get_attachment_image(
					$attachment_id,
					$size ?: 'medium_large',
					false,
					array( 'class' => 'acf-galerie-4-gallery__image' )
				);

				if ( ! $image ) {
					continue;
				}

				$items .= sprintf(
					'<li class="acf-galerie-4-gallery__item acf-galerie-4-gallery__item--image"><a href="%1$s">%2$s</a></li>',
					esc_url( $url ),
					$image
				);
				continue;
			}

			$title     = get_the_title( $attachment_id );
			$file_path = wp_parse_url( $url, PHP_URL_PATH );
			$file_type = strtoupper( pathinfo( is_string( $file_path ) ? $file_path : '', PATHINFO_EXTENSION ) );
			$items    .= sprintf(
				'<li class="acf-galerie-4-gallery__item acf-galerie-4-gallery__item--file"><a href="%1$s"><span class="acf-galerie-4-gallery__file-type">%2$s</span><span class="acf-galerie-4-gallery__file-title">%3$s</span></a></li>',
				esc_url( $url ),
				esc_html( $file_type ?: __( 'File', 'acf-galerie-4' ) ),
				esc_html( $title ?: basename( $url ) )
			);
		}

		if ( '' === $items ) {
			return '';
		}

		return '<ul class="acf-galerie-4-gallery">' . $items . '</ul>';
	}
}
