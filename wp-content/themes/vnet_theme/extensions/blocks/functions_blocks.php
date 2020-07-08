<?php

add_action('acf/render_field', 'blocks_add_preview');



function blocks_add_preview($field)
{
  if (isset($field['_name'])) {
    if ($field['_name'] === 'block_template') {
      $parent_id = isset($field['parent']) ? $field['parent'] : false;
      $parent_layout = isset($field['parent_layout']) ? $field['parent_layout'] : false;

      $layout = false;


      if ($parent_id && $parent_layout) {

        $post = get_post((int) $parent_id);

        if (!$post || is_wp_error($post)) return;
        $object = get_field_object($post->post_name);
        if (!$object || is_wp_error($object)) return;

        $layout = false;
        if (isset($object['layouts'])) {
          if (isset($object['layouts'][$parent_layout])) {
            $layout = &$object['layouts'][$parent_layout];
          }
        }
      } else {
        $post = get_post((int) $field['ID']);
        if (!$post || is_wp_error($post)) return;
        $parent = $post->post_parent;
        if (!$parent) return;
        $post = get_post((int) $parent);
        if (!$post || is_wp_error($post)) return;
        $layout = ['name' => $post->post_excerpt];
      }


      if (!$layout) return;

      $file = CURRENT_PATH . 'img/template-blocks/' . $layout['name'] . '.PNG';
      if (!file_exists($file)) return;
      $img = CURRENT_SRC . 'img/template-blocks/' . $layout['name'] . '.PNG';
?>
      <a class="template-link-preview" href="<?= $img; ?>" title="макет" target="_blank">
        <img src="<?= $img; ?>">
      </a>
    <?php
    }
  }
}



function get_block_spaces($padding = false, $margin = false)
{
  $paddingPrefix = 'p-';
  $marginPrefix = 'm-';

  $defaultPadding = 'xs';
  $defaultMargin = '0';

  $res = [
    'padding' => [
      'top' => get_space_class($padding, $paddingPrefix, 'top', $defaultPadding),
      'bottom' => get_space_class($padding, $paddingPrefix, 'bottom', $defaultPadding)
    ],
    'margin' => [
      'top' => get_space_class($margin, $marginPrefix, 'top', $defaultMargin),
      'bottom' => get_space_class($margin, $marginPrefix, 'bottom', $defaultMargin)
    ]
  ];
  return $res;
}


function get_space_class(&$arr, $prefix, $dir, $def)
{
  return get_from_array($arr, $dir, $prefix . $dir . '-' . $def, function ($item, $args) {
    if (!$item) return $args[0] . $args[1] . '-' . $args[2];
    if (is_array($item)) {
      if (isset($item[0])) return $args[0] . $args[1] . '-' . $item[0];
      return $args[0] . $args[1] . '-' . $args[2];
    }
    return $args[0] . $args[1] . '-' . $item;
  }, [$prefix, $dir, $def]);
}



function the_block_spaces(&$spaces)
{
  echo $spaces['padding']['top'] . ' ';
  echo $spaces['padding']['bottom'] . ' ';

  echo $spaces['margin']['top'] . ' ';
  echo $spaces['margin']['bottom'];
}



function is_display_acf_block(&$block = false)
{

  $default = true;

  if (!$block) return false;

  if (!is_array($block)) return $default;

  if (!isset($block['display'])) return $default;

  if (!$block['display']) return false;

  return $default;
}









function the_page_template()
{
  global $post;
  $template = get_field('page_block_template', $post->ID);
  if ($template) {
    foreach ($template as &$block) {
      if (is_display_acf_block($block)) {
        $key = $block['acf_fc_layout'];
        the_acf_layout_block($key, $block);
      }
    }
  }
}








function the_acf_layout_block($key, &$block)
{
  $prefix = 'layout';
  $padding = get_from_array($block, 'padding');
  $margin = get_from_array($block, 'margin');
  $white_bg = get_from_array($block, 'white_bg');

  if ($key === 'template_block') {
    $key    = $block['block'];
    $prefix = 'block';
    $block  = get_field($key, defined('BLOCKS_POST') ? BLOCKS_POST : 'option');
    if (!is_display_acf_block($block)) return;
  }
  require_template_block($prefix, $key, $block, $padding, $margin, $white_bg);
}








function require_template_block($prefix, $key, &$block, &$padding = false, &$margin = false, $white_bg = false)
{
  $file = CURRENT_PATH . 'template-blocks/' . $prefix . '-' . $key . '.php';
  if (file_exists($file)) {

    if (current_user_can('edit_posts')) {
      global $post;

      ob_start();

      require($file);

      $content = ob_get_clean();

      $post_id = $post ? $post->post_id : false;

      if ($prefix === 'block') {
        $post_id = BLOCKS_POST;
      }

      $fieldKey = get_post_meta($post_id, '_' . $key, true);
      $editLink = get_edit_post_link($post_id) . '#scrollto=acf-' . $fieldKey;

      $field = get_field_object($fieldKey);

      $label = $field['label'];

      $content = preg_replace("/data-admin/", "data-edit-type=\"$prefix\" data-edit-block=\"$key\" data-edit-file=\"$file\" data-edit-link=\"$editLink\" data-edit-label=\"$label\"", $content);
      echo $content;
    } else {
      require($file);
    }
  } else {
    ?>
    <span style="color:red; font-weight: bold">[block]</span>
    <strong><?= $file; ?></strong>
    <span style="color:red; font-weight: bold">[/block]</span>
<?php
  }
}




function get_layout($name, $block)
{
  if (!is_display_acf_block($block)) return;
  $file = CURRENT_PATH . 'template-blocks/layout-' . $name . '.php';
  if (!file_exists($file)) return;
  require($file);
}



function get_block($name, $block = false)
{
  if (!$block) {
    $block =  get_field($name, defined('BLOCKS_POST') ? BLOCKS_POST : 'option');
    if (!$block) return;
    if (!is_display_acf_block($block)) return;
  }
  $file = CURRENT_PATH . 'template-blocks/block-' . $name . '.php';
  if (!file_exists($file)) return;
  require($file);
}






function the_block($name, $prefix = 'block')
{

  $block = get_field($name, defined('BLOCKS_POST') ? BLOCKS_POST : 'option');

  if (!is_display_acf_block($block)) return;

  require_template_block($prefix, $name, $block);
}
