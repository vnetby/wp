<?php


/*
*
*                     IN DEVELOPMENT
*
*
*/

return;


// add_filter('save_post', 'save_block_to_template', 5);




function save_block_to_template($val = false, $id = false, $field = false)
{

  if (!isset($_POST['post_type'])) return;
  if ($_POST['post_type'] !== 'acf-field-group') return;

  $obj = acf_get_fields($_POST['post_ID']);
  if (empty($obj[0])) return;
  if (strpos($obj[0]['name'], 'block_') === false) return;

  $template = acf_get_field(BLOCKS_TEMPLATE_KEY);

  // file_put_contents(dirname(__FILE__) . '/___debug_post.json', json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  // file_put_contents(dirname(__FILE__) . '/___debug_object.json', json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  // file_put_contents(dirname(__FILE__) . '/___debug_template.json', json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


  $layout_row = blocks_create_layout_row($obj[0]);
  // file_put_contents(dirname(__FILE__) . '/___debug_layout_row.json', json_encode($layout_row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  $template['layouts'][$layout_row['key']] = $layout_row;
  // update_field(BLOCKS_TEMPLATE_KEY, $template);

  $group = acf_get_field_group(BLOCKS_TEMPLATE_GROUP_KEY);

  $group['fields'] = $template;

  // // $post = acf_get_field_group_post(BLOCKS_TEMPLATE_GROUP_KEY);
  // if ($post) {
    // $field_group['ID'] = $post->ID;
  // }
  // $field_group = acf_import_field_group($group);
  // file_put_contents(dirname(__FILE__) . '/___debug_last.json', json_encode($field_group, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}



function blocks_create_layout_row(&$item)
{
  $key = preg_replace("/field_/", "layout_", $item['key']);
  $name = preg_replace("/block_/", "", $item['name']);

  $label = $item['label'];
  $display = 'block';

  return [
    'key' => $key,
    'name' => $name,
    'label' => $label,
    'display' => 'block',
    'sub_fields' => $item['sub_fields']
  ];
}
