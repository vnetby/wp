<?php

add_action('init', 'set_about_company_constant');


function set_about_company_constant()
{
  define('ABOUT_POST', get_about_company_post_id());
  create_about_company_acf_group();
  $GLOBALS['about'] = get_field('about-company', ABOUT_POST);
}







function create_about_company_acf_group()
{

  if (function_exists('acf_import_field_group') && !has_about_company_acf_group()) {
    $about_company_acf_file = dirname(__FILE__) . "/acf.json";
    $about_company_acf = json_decode(file_get_contents($about_company_acf_file), true);

    if (isset($about_company_acf['key'])) {
      $about_company_acf = array($about_company_acf);
    }

    $ids = array();

    foreach ($about_company_acf as $field_group) {
      $post = acf_get_field_group_post($field_group['key']);
      if ($post) {
        $field_group['ID'] = $post->ID;
      }
      $field_group = acf_import_field_group($field_group);
      $ids[] = $field_group['ID'];
    }
  }
}








function has_about_company_acf_group()
{
  global $wpdb;
  $table = $wpdb->prefix . 'posts';
  $group = $wpdb->get_results("SELECT `post_name` FROM $table WHERE `post_type` LIKE 'acf-field-group' AND `post_name` LIKE 'group_5db6b688a1556' LIMIT 1");
  if (!$group || is_wp_error($group)) return false;
  if (!is_array($group)) return false;
  if (!isset($group[0])) return false;
  return true;
}






function get_about_company_post_id()
{
  $post = get_about_company_post();
  if (!$post) {
    $post_data = [
      'post_title'    => 'About company',
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_author'   => 1,
      'post_type' => 'about_company'
    ];
    $post_id = wp_insert_post($post_data);
    return $post_id;
  }
  return $post->ID;
}





function get_about_company_post()
{
  $posts = get_posts([
    'numberposts' => 1,
    'post_type'   => 'about_company',
  ]);
  if (!$posts) return false;
  if (!is_array($posts)) return false;
  if (!count($posts)) return false;
  return $posts[0];
}
