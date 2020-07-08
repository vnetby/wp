<?php

/**
 * vnet-theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package vnet-theme
 */

define('DISALLOW_FILE_EDIT', true);

require(THEME_PATH . 'extensions/install.php');
require(THEME_PATH . 'admin/functions.php');


add_action('after_setup_theme', 'vnet_theme_setup');

add_action('after_setup_theme', 'vnet_theme_content_width', 0);

add_action('widgets_init', 'vnet_theme_widgets_init');

add_action('init', 'edit_post_supports', 50);

add_filter('get_avatar_url', 'change_root_avatar', 10, 3);

add_action('wp_enqueue_scripts', 'register_jquery');

add_action('nav_menu_css_class', 'add_current_nav_class', 10, 2);

add_filter('upload_mimes', 'cc_mime_types');

add_action('admin_head', 'add_back_dates_var');

add_action('wp_head', 'add_back_dates_var');

add_action('init', 'vnet_remove_post_type_supports');

add_action('init', 'register_post_taxonomies');




function register_post_taxonomies()
{
  // return;
  register_taxonomy('page_tags', ['page'], [
    'labels' => [
      'name'              => 'Метки',
      'singular_name'     => 'Метка',
      'search_items'      => 'Найти метку',
      'all_items'         => 'Все метки',
      'view_item '        => 'Открыть метку',
      'edit_item'         => 'Изменить метку',
      'update_item'       => 'Обновить метку',
      'add_new_item'      => 'Добавить метку',
      'new_item_name'     => 'Новая метка',
      'menu_name'         => 'Метки',
    ],

    'description'             => 'Метки страниц',
    'public'                  => true,
    'show_ui'                 => true,
    'show_in_menu'            => 'page',
    'show_in_nav_menus'       => true,
    'show_tagcloud'           => true,
    'show_in_rest'            => null,
    'rest_base'               => null,
    'publicly_queryable'      => true,
    'hierarchical'            => false,
    'rewrite'                 => true,
    // 'rewrite'                 => ['slug' => 'tag', 'with_front' => false],
    'meta_box_cb'             => 'post_categories_meta_box',
    'show_admin_column'       => true,
    '_builtin'                => false,
    'show_in_quick_edit'      => null,
    'sort'                    => true
  ]);
}


function vnet_remove_post_type_supports()
{
  remove_post_type_support('page', 'editor');
}






function cc_mime_types($mimes)
{
  $mimes['svg'] = 'file';
  return $mimes;
}




function add_back_dates_var()
{
  $user = false;
  if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if ($user) {
      $user = json_encode($user, JSON_UNESCAPED_UNICODE);
    }
  }
?>
  <script>
    var responsive = {
      mobile: 768,
      tablet: 1200
    }

    var woof_lang = {
      'orderby': "",
      'date': "по дате",
      'perpage': "per page",
      'pricerange': "цена",
      'menu_order': "исходная сортировка",
      'popularity': "по популярности",
      'rating': "по рейтингу",
      'price': "по возрастанию цены",
      'price-desc': "по убыванию цены"
    };

    var woof_lang_loading = "Поиск ...";

    var back_dates = {
      'ajax_url': '<?= admin_url("admin-ajax.php"); ?>',
      'SRC': '<?= CURRENT_SRC; ?>',
      'url': '<?= get_site_url(); ?>',
      'catalog': '<?= get_permalink(get_page_by_path('shop')); ?>',
      'block_post': '<?= defined('BLOCKS_POST') ? BLOCKS_POST : false; ?>',
      'about_post': '<?= defined('ABOUT_POST') ? ABOUT_POST : false; ?>',
      'user': '<?= $user; ?>'
    };
  </script>

<?php
}



function add_current_nav_class($classes, $item)
{
  return $classes;
  if (is_product_category()) {
    $object  = get_queried_object();
    $_id     = $object->term_id;
    $shop_id = get_option('woocommerce_shop_page_id');
    if ($shop_id === $item->object_id) {
      $classes[] = 'current-menu-item';
    }
  } else {
    if (is_single()) {
      global $post;
      $_id = $post->ID;
      $current_post_type      = get_post_type_object(get_post_type($_id));
      $current_post_type_slug = $current_post_type->rewrite['slug'];
      $menu_slug              = strtolower(trim($item->url));
      if (strpos($menu_slug, $current_post_type_slug) !== false) {
        $classes[] = 'current-menu-item';
      }
    }
  }

  return $classes;
}






function change_set_search_box($s_options)
{
  // $s_options['defaultsearchtext']   = 'Поиск ...';
  $s_options['showmoreresultstext'] = 'Еще ...';
  $s_options['noresultstext']       = 'Нет результатов!';
  $s_options['didyoumeantext']      = 'Возможно Вы искали:';
  return $s_options;
}








function register_jquery()
{
  wp_deregister_script('jquery');
  wp_register_script('jquery', SRC . 'assets/jquery3/jquery3.min.js');
  wp_enqueue_script('jquery');
}







function change_root_avatar($url, $id_or_email, $args)
{
  if (gettype($id_or_email) === 'string' || gettype($id_or_email) === 'integer') {
    if ($id_or_email == 1) {
      return SRC . 'img/root-avatar.jpg';
    }
  }

  if (gettype($id_or_email) === 'object') {
    if ($id_or_email->user_id == 1) {
      return SRC . 'img/root-avatar.jpg';
    }
  }
  return $url;
}





function edit_post_supports()
{
  remove_post_type_support('page', 'excerpt');
  remove_post_type_support('page', 'author');
  remove_post_type_support('page', 'revisions');
  remove_post_type_support('page', 'trackbacks');
  remove_post_type_support('page', 'post-formats');
  // register_taxonomy_for_object_type('post_tag', 'page');
  // register_taxonomy_for_object_type ( 'category', 'news' );
}





function vnet_theme_setup()
{
  load_theme_textdomain('vnet_theme', THEME_PATH . 'languages');
  add_theme_support('automatic-feed-links');
  add_theme_support('menus');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  register_nav_menus([
    'top_menu'     => 'Главное меню',
    'foot_menu'    => 'Меню в подвале'
  ]);
  add_theme_support('html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
  ));

  add_theme_support('custom-background', apply_filters('vnet_theme_custom_background_args', array(
    'default-color' => 'ffffff',
    'default-image' => '',
  )));

  add_theme_support('customize-selective-refresh-widgets');

  add_theme_support('custom-logo', array(
    'height'      => 250,
    'width'       => 250,
    'flex-width'  => true,
    'flex-height' => true,
  ));

  add_theme_support('woocommerce');
  add_theme_support('wc-product-gallery-zoom');
  add_theme_support('wc-product-gallery-lightbox');
  add_theme_support('wc-product-gallery-slider');
}




function vnet_theme_content_width()
{

  $GLOBALS['content_width'] = apply_filters('vnet_theme_content_width', 640);
}



function vnet_theme_widgets_init()
{

  register_sidebar(array(
    'name'          => 'Меню каталог продукции',
    'id'            => 'catalog',
    'description'   => 'Добавьте сюда категории.',
    'before_widget' => '<div class="wc-cat-sidebar">',
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ));


  // register_sidebar(array(
  //   'name'          => esc_html__('Сайдбар в калалоге', 'vnet_theme'),
  //   'id'            => 'catalog_sidebar',
  //   'description'   => esc_html__('Добавьте сюда элементы.', 'vnet_theme'),
  //   'before_widget' => '<div class="wc-cat-sidebar">',
  //   'after_widget'  => '</div>',
  //   'before_title'  => '<h2 class="widget-title">',
  //   'after_title'   => '</h2>',
  // ));
}
