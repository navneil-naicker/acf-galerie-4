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
 * Version: 1.2.0
 * Domain Path: /lang
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ACFG4_VERSION', '1.2.0' );
define( 'ACFG4_PLUGIN', __FILE__ );
define( 'ACFG4_PLUGIN_BASENAME', plugin_basename( ACFG4_PLUGIN ) );
define( 'ACFG4_PLUGIN_NAME', trim( dirname( ACFG4_PLUGIN_BASENAME ), '/' ) );
define( 'ACFG4_PLUGIN_DIR', untrailingslashit( dirname( ACFG4_PLUGIN ) ) );

//Load the text domain
add_action('init', 'acfg4_textdomain');
function acfg4_textdomain() {
	load_plugin_textdomain( 'acf-galerie-4', false, basename( dirname( __FILE__ ) ) . '/lang' );
}

//Registers the ACF field type.
add_action( 'init', 'acfg4_init_register_type' );
function acfg4_init_register_type() {
	if ( ! function_exists( 'acf_register_field_type' ) ) return;
	require_once __DIR__ . '/class-acfg4-register-field-type.php';
	acf_register_field_type( 'acfg4_register_field_type' );
}

function acfg4_get_gallery( $attachment_ids ){
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

add_action( 'wpgraphql/acf/registry_init', function() {
	require_once __DIR__ . '/providers/class.wpgraphql.php';
	new acfg4_wpgraphql();
});