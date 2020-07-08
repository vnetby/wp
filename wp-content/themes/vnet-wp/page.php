<?php
get_header();
global $post;
$className = get_field('page_class', $post->ID);
?>
<div class="site-wrap<?= $className ? ' ' . $className : ''; ?>">
  <?= vnet_get_template('template-header'); ?>
  <main class="main-content">
    <?php
    the_page_template();
    ?>
  </main>
  <?= vnet_get_template('template-footer'); ?>
</div>
<?php
get_footer();
