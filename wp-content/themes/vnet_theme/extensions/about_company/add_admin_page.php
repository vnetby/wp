<?php
add_action('admin_menu', 'register_about_company_admin_page');

function register_about_company_admin_page()
{
  add_menu_page('О компании', 'О компании', 'administrator', 'post.php?post=' . ABOUT_POST . '&action=edit', null, 'dashicons-building', 6);
}
