<?php

/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!empty($breadcrumb)) {

	echo $wrap_before;

	if (is_product_category() && is_paged()) {
		$breadcrumb = array_slice($breadcrumb, 0, count($breadcrumb) - 1);
	}

	if (is_product_category() || is_product()) {
		$page = vnet_get_catalog_page();
		$title = $page->post_title;
		$link = get_permalink($page->ID);
		array_splice($breadcrumb, 1, 0, [[$title, $link]]);
	}
	global $wp_query;

	if (isset($_GET['really_curr_tax'])) {
		$id = (int) $_GET['really_curr_tax'];
		$catsPath = [];
		$cat = get_term($id, 'product_cat');
		$catsPath[] = $cat;
		if ($cat->parent) {
			while ($cat->parent) {
				$cat = get_term($cat->parent, 'product_cat');
				$catsPath[] = $cat;
			}
		}
		$catsPath = array_reverse($catsPath);
		$breadcrumb = array_slice($breadcrumb, 0, 2);
		foreach ($catsPath as &$item) {
			$breadcrumb[] = [$item->name, get_term_link($item->term_id)];
		}
	}

	foreach ($breadcrumb as $key => $crumb) {

		echo $before;

		if (!empty($crumb[1]) && sizeof($breadcrumb) !== $key + 1) {
			echo '<a href="' . esc_url($crumb[1]) . '">' . esc_html($crumb[0]) . '</a>';
		} else {
			echo esc_html($crumb[0]);
		}

		echo $after;

		if (sizeof($breadcrumb) !== $key + 1) {
			echo $delimiter;
		}
	}

	echo $wrap_after;
}
