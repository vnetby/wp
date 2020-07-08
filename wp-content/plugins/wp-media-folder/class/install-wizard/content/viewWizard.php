<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
$image_src = WPMF_PLUGIN_URL . 'class/install-wizard/content/welcome-illustration.png';
?>
<form method="post">
    <div class="start-wizard">
        <div class="start-wizard-image">
            <img src="<?php echo esc_url($image_src); ?>"
                 srcset=""
                 class="Illustration---Done" />
        </div>
        <div class="start-wizard-container">
            <div class="title">
                <?php esc_html_e('Welcome to WP Media Folder Settings first configuration wizard!', 'wpmf') ?>
            </div>
            <p class="description">
                <?php esc_html_e('This wizard will help you with some server compatibility check and with plugin main configuration. Follow some simple steps and get a powerful media library in no time', 'wpmf') ?>
            </p>
        </div>
        <div class="start-wizard-footer configuration-footer">
            <a href="<?php echo esc_url(add_query_arg('step', 'environment', remove_query_arg('activate_error')))?>" class="next-button">
                <?php esc_html_e('Continue to environment check', 'wpmf'); ?>
            </a>

            <a href="<?php echo esc_url(admin_url('options-general.php?page=option-folder'))?>" class="backup-button">
                    <?php esc_html_e('I know what I\'m doing, skip wizard', 'wpmf'); ?></a>
        </div>
    </div>
</form>