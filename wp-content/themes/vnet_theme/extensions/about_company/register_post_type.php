<?php

add_action ( 'init', 'register_about_company_post_type' );

add_action( 'admin_init', 'remove_about_company_post_supports' );



function remove_about_company_post_supports () {
  remove_post_type_support( 'about_company', 'editor' );
  remove_post_type_support( 'about_company', 'title' );
  remove_post_type_support( 'about_company', 'author' );
  remove_post_type_support( 'about_company', 'excerpt' );
  remove_post_type_support( 'about_company', 'trackbacks' );
  remove_post_type_support( 'about_company', 'comments' );
  remove_post_type_support( 'about_company', 'revisions' );
  remove_post_type_support( 'about_company', 'page-attributes' );
  remove_post_type_support( 'about_company', 'post-formats' );
}


function register_about_company_post_type () {

  register_post_type ( 'about_company', [
    'labels' => [
      'name'              => 'О компании',
      'singular_name'     => 'О компании',
      'edit_item'         => 'Редактировать информацию о компании',
      'parent_item_colon' => '',
      'menu_name'         => 'О компании'
    ],
    'description'           => '',
    'public'                => true,
    'publicly_queryable'    => true,
    'exclude_from_searc'    => false,
    'show_u'                => false,
    'show_in_menu'          => false,
    // 'show_in_menu'       => true,
    // 'show_in_menu'       => 'post.php?post=72&action=edit',
    // 'show_in_admin_bar'  => false,
    'show_in_nav_menus'     => false,
    'show_in_res'           => true,
    'rest_base'             => 'about_company',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'menu_position'         => 21,
    'menu_icon'             => 'dashicons-building',
    'capability_type'       => 'post',
    'map_meta_cap'          => true,
    'hierarchica'           => false,
    'supports'              => [],
    'capabilities'          => ['create_posts' => 'do_not_allow'],
    // 'taxonomies'         => ['news_cat'],
    'has_archive'           => true,
    'rewrite'               => true,
    'can_export'            => true,
    'delete_with_use'       => false,
    'query_var'             => '/?{query_var_string}={post_slug}',
    '_builtin'              => false,
    '_edit_link'            => 'post.php?post=%d'
  ]);

}

