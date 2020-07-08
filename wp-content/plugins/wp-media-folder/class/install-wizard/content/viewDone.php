<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
$image_src = WPMF_PLUGIN_URL . 'class/install-wizard/content/checklist-icon.png';
$srcset2x = WPMF_PLUGIN_URL . 'class/install-wizard/content/done/done-illustration@2x.png';
$srcset3x = WPMF_PLUGIN_URL . 'class/install-wizard/content/done/done-illustration@3x.png';
?>
<div class="wizard-content-done">
    <div class="wizard-done">
        <div class="wizard-done-image">
            <img src="<?php echo esc_url(WPMF_PLUGIN_URL . 'class/install-wizard/content/done/done-illustration.png'); ?>"
                 srcset="<?php echo esc_url($srcset2x); ?> 2x,<?php echo esc_url($srcset3x); ?> 3x" class="Illustration---Done">

        </div>
        <div class="wizard-done-container">
            <div class="title"><?php esc_html_e('Done', 'wpmf') ?></div>
            <p class="description">
                <?php esc_html_e('You have now completed the plugin quick configuration', 'wpmf') ?>
            </p>
        </div>
        <div class="wizard-done-footer configuration-footer">
            <a href="<?php echo esc_url(admin_url('upload.php')) ?>" class="button">
                <?php esc_html_e('GO TO MEDIA LIBRARY', 'wpmf'); ?></a>
        </div>
    </div>
</div>
