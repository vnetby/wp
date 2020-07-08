<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
$wizard = new WpmfInstallWizard();
// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- View request, no action
$step      = isset($_GET['step']) ? sanitize_key($_GET['step']) : '';
$next_link = $wizard->getNextLink($step);
?>

<form method="post" id="quick-config-form">
    <?php wp_nonce_field('wpmf-setup-wizard', 'wizard_nonce'); ?>
    <input type="hidden" name="wpmf_save_step" value="1"/>
    <div class="wizard-header">
        <div class="title font-size-35"><?php esc_html_e('Image Configuration', 'wpmf'); ?></div>
        <p class="description"><?php esc_html_e('We will guide you through the plugin main settings. You can also configure it later and skip the wizard', 'wpmf') ?></p>
    </div>
    <div class="wizard-content">
        <div class="ju-settings-option wpmf_width_100 wpmf-no-shadow">
            <div class="wpmf_row_full">
                <input type="hidden" name="wpmf_usegellery" value="0">
                <label class="ju-setting-label text">
                    <?php esc_html_e('WP Media Folder Galleries', 'wpmf') ?>
                </label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="wpmf_usegellery" value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="description text_left p-d-20">
                    <?php esc_html_e('Enhance the Wordpress default gallery system by adding themes and additional parameters in the gallery manager', 'wpmf'); ?>
                </p>
            </div>
        </div>

        <div class="ju-settings-option  wpmf_width_100 wpmf-no-shadow">
            <div class="wpmf_row_full">
                <input type="hidden" name="wpmf_usegellery_lightbox" value="0">
                <label class="ju-setting-label text">
                    <?php esc_html_e('Gallery Lightbox', 'wpmf') ?></label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="wpmf_usegellery_lightbox" value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="description text_left p-d-20">
                    <?php esc_html_e('Add lightbox to images in Wordpress default galleries', 'wpmf'); ?>
                </p>
            </div>
        </div>
    </div>

    <div class="wizard-footer">
        <div class="wpmf_row_full">
            <input type="submit" value="<?php esc_html_e('Continue', 'wpmf'); ?>" class="m-tb-20"
                   name="wpmf_save_step"/>
        </div>

        <a href="<?php echo esc_url(admin_url('options-general.php?page=option-folder'))?>" class="go-to-dash"><span><?php esc_html_e('I know what I\'m doing, skip wizard', 'wpmf'); ?></span></a>
    </div>
</form>