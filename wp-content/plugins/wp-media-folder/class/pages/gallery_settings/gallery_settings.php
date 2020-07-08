<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
// phpcs:disable WordPress.Security.EscapeOutput -- Content already escaped in the method
echo $default_theme;
echo $portfolio_theme;
echo $masonry_theme;
echo $slider_theme;
// phpcs:enable