<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<form method="post">
    <?php wp_nonce_field('wpmf-setup-wizard', 'wizard_nonce'); ?>
    <div class="wizard-header large-content-width">
        <div class="title font-size-35"><?php esc_html_e('Additional Features', 'wpmf'); ?></div>
        <p class="description"><?php esc_html_e('We will guide you through the plugin main settings, You can also onfigure it later and skip the wizard', 'wpmf') ?></p>
    </div>
    <div class="wizard-content large-content-width">
        <div class="ju-settings-option cboption" style="height: 200px; overflow: hidden; text-overflow: ellipsis; margin-right: 20px">
            <div class="wpmf_row_full p-d-20">
                <input type="hidden" name="wpmf_option_mediafolder" value="0">
                <label data-alt="<?php esc_html_e('Load WP Media Folder files on frontend. Activate it if
             you want to use a frontend page builder along with the media manager', 'wpmf'); ?>"
                       class="ju-setting-label text"><?php esc_html_e('WP Media Folder on frontend', 'wpmf') ?></label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" id="cb_option_mediafolder" name="wpmf_option_mediafolder"
                               value="1">
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="description text_left p-d-20 border-top-e4e8ed">
                    <?php esc_html_e('Load WP Media Folder files on frontend. Activate it if
             you want to use a frontend page builder along with the media manager', 'wpmf'); ?>
                </p>
            </div>
        </div>

        <div class="ju-settings-option cboption" style="height: 200px; overflow: hidden; text-overflow: ellipsis;">
            <div class="wpmf_row_full p-d-20">
                <input type="hidden" name="hide_remote_video" value="0">
                <label data-alt="<?php esc_html_e('Enable or disable remote video feature', 'wpmf'); ?>"
                       class="ju-setting-label text"><?php esc_html_e('Enable remote video feature', 'wpmf') ?></label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="hide_remote_video"
                               value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="description text_left p-d-20 border-top-e4e8ed">
                    <?php esc_html_e('Enable or disable remote video feature', 'wpmf'); ?>
                </p>
            </div>
        </div>

        <div class="ju-settings-option cboption" style="height: 200px; overflow: hidden; text-overflow: ellipsis; margin-right: 20px">
            <div class="wpmf_row_full p-d-20">
                <input type="hidden" name="wpmf_option_override" value="0">
                <label data-alt="<?php esc_html_e('Possibility to replace an existing file by another one.', 'wpmf'); ?>"
                       class="ju-setting-label text"><?php esc_html_e('Override file', 'wpmf') ?></label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" id="cb_option_override"
                               name="wpmf_option_override" value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <p class="description text_left p-d-20 border-top-e4e8ed">
                    <?php esc_html_e('Possibility to replace an existing file by another one.', 'wpmf'); ?>
                </p>
            </div>
        </div>

        <div class="ju-settings-option cboption" style="height: 200px; overflow: hidden; text-overflow: ellipsis;">
            <div class="wpmf_row_full p-d-20">
                <input type="hidden" name="wpmf_option_duplicate" value="0">
                <label data-alt="<?php esc_html_e('Add a button to duplicate a media from the media manager', 'wpmf'); ?>"
                       class="ju-setting-label text"><?php esc_html_e('Duplicate file', 'wpmf') ?>
                </label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" id="cb_option_duplicate"
                               name="wpmf_option_duplicate" value="1" checked>
                        <span class="slider round"></span>
                    </label>
                </div>

                <p class="description text_left p-d-20 border-top-e4e8ed">
                    <?php esc_html_e('Add a button to duplicate a media from the media manager', 'wpmf'); ?>
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