<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
$allow_tabs_submit = array(
    'general',
    'media_access',
    'wordpress_gallery',
    'sync_media',
    'gallery_addon',
    'files_folders',
    'cloud'
);
?>
<?php if (in_array($tab['id'], $allow_tabs_submit)) : ?>
<div class="btn_wpmf_saves">
    <button type="submit" name="btn_wpmf_save"
            id="btn_wpmf_save"
            class="btn_wpmf_save ju-button orange-button waves-effect waves-light"><?php esc_html_e('Save Changes', 'wpmf'); ?></button>
</div>
<?php endif; ?>