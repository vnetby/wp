<?php

/**

 * The template for displaying search results pages

 *

 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result

 *

 * @package vnet-theme

 */



get_header();


if (have_posts()) {

  echo 'search';


  get_footer();
}
