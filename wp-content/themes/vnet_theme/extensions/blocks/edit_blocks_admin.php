<?php

// add_action('edit_form_after_editor', 'myprefix_edit_form_after_editor');

// function myprefix_edit_form_after_editor()
// {
//   global $post;
//   if (!$post) return;
//   if ((int) $post->ID !== (int) BLOCKS_POST) return;

//   $fields = acf_get_field('page_block_template');
//   if (!$fields) return;

//   if (!isset($fields['layouts'])) return;

//   if (!is_array($fields['layouts'])) return;

//   $layouts = &$fields['layouts'];

//   $acf_group = new acf_field__group;

//   foreach ($layouts as $item) {
//     // print_r ($item);
//     foreach ( $item['sub_fields'] as $sub ) {

//       // render_acf_block($sub);
//     }
//   }
// }



// function render_acf_block(&$field_group)
// {
//   // vars
//   $id = "acf-{$field_group['key']}";      // acf-group_123
//   $title = $field_group['key'];        // Group 1
//   $context = 'normal';    // normal, side, acf_after_title
//   $priority = 'high';              // high, core, default, low

//   // Localize data
//   $postboxes[] = array(
//     'id'		=> $id,
//     'key'		=> $field_group['key'],
//     'style'		=> 'block',
//     'label'		=> '',
//     'edit'		=> acf_get_field_group_edit_link( $field_group['ID'] )
//   );


//   add_meta_box( $id, $title, ['ACF_Form_Post','render_meta_box'], 'the_blocks', $context, $priority, array('field_group' => $field_group) );
// }
