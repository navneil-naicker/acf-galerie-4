<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class acfg4_wpgraphql {

    /**
     * Initializes and registers the custom GraphQL types and fields.
     *
     * The constructor is responsible for:
     * - Registering the custom GraphQL object type using the `register_graphql_object_type` method.
     * - Registering the custom ACF field type with GraphQL using the `register_graphql_acf_field_type` method.
     *
     * This setup ensures that the custom ACF field type and associated GraphQL object type 
     * are available for querying and mutation in the WPGraphQL schema.
     *
     * @return void
     */
    public function __construct(){
        // Register the custom GraphQL object type
        $this->register_graphql_object_type();

        // Register the custom ACF field type with GraphQL
        $this->register_graphql_acf_field_type();
    }

    /**
     * Registers the custom GraphQL object type for ACF Galerie 4.
     *
     * This method defines the structure and fields for the ACF Galerie 4 object type 
     * that will be exposed in the GraphQL schema. The object type includes various fields 
     * related to attachment metadata, such as post information, file details, and various image sizes.
     * These fields will be available for querying through the WPGraphQL API.
     *
     * Fields include:
     * - Attachment information such as ID, author, status, content, and date-related fields.
     * - Metadata about the associated file, including full, medium, large, and thumbnail sizes, 
     *   with their respective dimensions, MIME types, file sizes, and URLs.
     *
     * This custom GraphQL object type provides flexibility for retrieving detailed media information 
     * and is particularly useful for building galleries or media-rich applications using WordPress.
     *
     * @return void
     */
    public function register_graphql_object_type(){
        register_graphql_object_type(
            'ACF_Galerie_4',
            [
                'description' => __( 'Registered schema for ACF Galerie 4', 'acf-galerie-4' ),
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
                        'description' => __( 'Medium file name.', 'acf-galerie-4' ),
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
                        'description' => __( 'Large file name.', 'acf-galerie-4' ),
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
                        'description' => __( 'Thumbnail file name.', 'acf-galerie-4' ),
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
                        'description' => __( 'Medium large file name.', 'acf-galerie-4' ),
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
    }

    /**
     * Registers the ACF field type with GraphQL for ACF Galerie 4.
     *
     * This method registers the ACF field type for use with WPGraphQL, specifically 
     * for the "galerie-4" field type. It configures the field to return a list of 
     * 'ACF_Galerie_4' objects and defines a resolver function to fetch and transform 
     * the gallery's attachments. The resolver function ensures that only non-empty and 
     * valid attachment values are returned in the GraphQL query result.
     *
     * The method performs the following actions:
     * - Excludes the 'graphql_non_null' field from the admin side of GraphQL.
     * - Defines the return type as a list of 'ACF_Galerie_4' objects.
     * - Implements a custom resolver function that:
     *     - Resolves the field value using the field's configuration.
     *     - Filters out any empty values.
     *     - Uses the `acfg4_register_field_type` class to retrieve and process the attachments.
     *     - Transforms and returns the valid attachments after filtering.
     *
     * This custom registration ensures that ACF Galerie 4 fields can be queried 
     * via the GraphQL API, and their attachments can be properly processed and returned.
     *
     * @return void
     */
    public function register_graphql_acf_field_type(){
        register_graphql_acf_field_type(
            'galerie-4',
            [
                'exclude_admin_fields' => [ 'graphql_non_null' ],
                'graphql_type' => [ 'list_of' => 'ACF_Galerie_4' ],
                'resolve' => function ( $root, $args, $context, $info, $field_type, $field_config ) {
                    $value = $field_config->resolve_field( $root, $args, $context, $info );

                    if ( empty( $value ) ) return null; 

                    $value = array_filter( $value );

                    $register = new acfg4_register_field_type();
                    $attachments = $register->get_attachments( $value );

                    return array_filter( $this->transform( $attachments ) );
                },
            ]
        );
    }

    /**
     * Transforms a list of attachments into a GraphQL-compatible format.
     *
     * This method processes an array of attachments, transforming each attachment's data 
     * into a format that is suitable for GraphQL responses. The transformed data includes 
     * the attachment's post metadata, as well as the file's attributes such as URL, MIME type, 
     * size, and dimensions. Special handling is applied for image files and non-image files.
     *
     * The method performs the following actions:
     * 1. Loops through each attachment and collects metadata like post author, date, content, 
     *    title, status, etc.
     * 2. For non-image attachments (files), the method fetches the file URL, MIME type, 
     *    file size, and includes them in the response.
     * 3. For image attachments, the method retrieves the full image metadata (including file, 
     *    width, height, and MIME type), and also processes various image sizes (thumbnail, 
     *    medium, large, medium_large) to include their respective URLs, dimensions, and file sizes.
     * 4. Returns an array of transformed attachment data, which is ready to be included in 
     *    the GraphQL response.
     *
     * @param array $attachments An array of attachment objects to be transformed.
     * @return array A transformed array of attachment data in a GraphQL-friendly format.
     */
    public function transform( $attachments ){
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
}