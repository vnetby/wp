<?php
add_action ( 'admin_head', 'vnet_add_admin_scripts' );

add_action ( 'save_post', 'vnet_add_acf_blocks' );

add_action ( 'acf/render_field', 'vnet_template_render_field', 10, 1 );




function vnet_template_render_field ( $field ) {
  // global $post;
  // print_r ($post->post_type );
}





function vnet_add_admin_scripts ()  {

  wp_enqueue_script( 'admin-main-scripts', SRC . 'admin/js/main.js');
  wp_enqueue_style( 'admin-main-css', SRC . 'admin/css/main.css');

  wp_enqueue_script( 'admin-fancybox', SRC . 'admin/assets/fancybox/jquery.fancybox.min.js');
  wp_enqueue_style( 'admin-fancybox', SRC . 'admin/assets/fancybox/jquery.fancybox.min.css');

}





function vnet_add_acf_blocks ( $post_id ) {
  // $post = get_post ( $post_id );
  // if ( $post->post_type === 'acf_field_group' ) {
  //
  // }
  // file_put_contents( dirname ( __FILE__ ) . '/____debug.json', json_encode( $post, JSON_PRETTY_PRINT ) );
}
