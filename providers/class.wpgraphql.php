<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class acfg4_wpgraphql {

    public function __construct(){
        $this->register_graphql_object_type();
        $this->register_graphql_acf_field_type();
    }

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

    public function register_graphql_acf_field_type(){
        register_graphql_acf_field_type(
            'galerie-4',
            [
                'exclude_admin_fields' => [ 'graphql_non_null' ],
                'graphql_type' => [ 'list_of' => 'ACF_Galerie_4' ],
                'resolve' => static function ( $root, $args, $context, $info, $field_type, $field_config ) {
                    $value = $field_config->resolve_field( $root, $args, $context, $info );
                    file_put_contents( __DIR__ . '/output.txt', print_r($value, true) );

                    if ( empty( $value ) ) return null; 

                    $value = array_filter( $value );

                    return array_filter( acfg4_get_gallery( $value ) );
                },
            ]
        );
    }
}