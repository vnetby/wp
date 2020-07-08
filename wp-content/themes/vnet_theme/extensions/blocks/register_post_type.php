<?php
add_action ('admin_menu', 'register_blocks_admin_page');

add_action ( 'init', 'register_blocks_post_type' );

add_action( 'admin_init', 'remove_blocks_post_type_support' );



function remove_blocks_post_type_support () {
  remove_post_type_support( 'the_blocks', 'editor' );
  remove_post_type_support( 'the_blocks', 'title' );
  remove_post_type_support( 'the_blocks', 'author' );
  remove_post_type_support( 'the_blocks', 'excerpt' );
  remove_post_type_support( 'the_blocks', 'trackbacks' );
  remove_post_type_support( 'the_blocks', 'comments' );
  remove_post_type_support( 'the_blocks', 'revisions' );
  remove_post_type_support( 'the_blocks', 'page-attributes' );
  remove_post_type_support( 'the_blocks', 'post-formats' );
}


function register_blocks_post_type () {

  register_post_type ( 'the_blocks', [
    'labels' => [
      'name'              => 'Блоки',
      'singular_name'     => 'Блоки',
      'edit_item'         => 'Редактировать блоки',
      'parent_item_colon' => '',
      'menu_name'         => 'Блоки'
    ],
    'description'           => '',
    'public'                => true,
    'publicly_queryable'    => true,
    'exclude_from_search'   => false,
    'show_u'                => false,
    'show_in_menu'          => false,
    // 'show_in_menu'       => true,
    // 'show_in_menu'       => 'the_blocks_page',
    // 'show_in_admin_bar'  => false,
    'show_in_nav_menus'     => false,
    'show_in_res'           => true,
    'rest_base'             => 'the_blocks',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'menu_position'         => 21,
    'menu_icon'             => 'dashicons-align-left',
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


function register_blocks_admin_page () {
  // add_menu_page( 'Блоки', 'Блоки', 'administrator', 'the_blocks_page', null, 'dashicons-align-left', 6 );
  add_menu_page( 'Блоки', 'Блоки', 'administrator', 'post.php?post='.BLOCKS_POST.'&action=edit', null, 'dashicons-align-left', 6 );
}
