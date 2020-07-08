<?php

add_action('init', 'set_blocks_constant');


function set_blocks_constant()
{
  define('BLOCKS_POST', get_blocks_post_id());
  create_blocks_acf_group();
}







function create_blocks_acf_group()
{
  if (function_exists('acf_import_field_group') && !has_blocks_acf_group()) {
    $blocks_acf_file = dirname(__FILE__) . "/acf_template.json";
    if ( !file_exists( $blocks_acf_file ) ) return;
    $blocks_acf = json_decode(file_get_contents($blocks_acf_file), true);

    if (isset($blocks_acf['key'])) {
      $blocks_acf = array($blocks_acf);
    }

    $ids = array();

    foreach ($blocks_acf as $field_group) {
      $post = acf_get_field_group_post($field_group['key']);
      if ($post) {
        $field_group['ID'] = $post->ID;
      }
      $field_group = acf_import_field_group($field_group);
      $ids[] = $field_group['ID'];
    }
  }
  
}








function has_blocks_acf_group()
{
  $key = BLOCKS_TEMPLATE_KEY;
  global $wpdb;
  $table = $wpdb->prefix . 'posts';
  $group = $wpdb->get_results("SELECT `post_name` FROM $table WHERE `post_type` LIKE 'acf-field' AND `post_name` LIKE '$key' LIMIT 1");
  if (!$group || is_wp_error($group)) return false;
  if (!is_array($group)) return false;
  if (!isset($group[0])) return false;
  return true;
}






function get_blocks_post_id()
{
  $post = get_blocks_post();
  if (!$post) {
    $post_data = [
      'post_title'    => 'Блоки',
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type' => 'the_blocks'
    ];
    $post_id = wp_insert_post($post_data);
    return $post_id;
  }
  return $post->ID;
}





function get_blocks_post()
{
  $posts = get_posts([
    'numberposts' => 1,
    'post_type'   => 'the_blocks',
  ]);
  if (!$posts) return false;
  if (!is_array($posts)) return false;
  if (!count($posts)) return false;
  return $posts[0];
}
