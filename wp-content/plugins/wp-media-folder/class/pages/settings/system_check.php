<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="content-box wpmf_width_80">
        <p class="description text_center" style="margin: 0">
            <?php esc_html_e('We have checked your server environment. 
            If you see some warning below it means that some plugin features may not work properly.
            Reload the page to refresh the results', 'wpmf'); ?>
        </p>
            <div class="wpmf_width_100 p-tb-20 wpmf_left text label_text"><?php esc_html_e('PHP Version', 'wpmf'); ?></div>
            <div class="ju-settings-option wpmf_width_100">
                <div class="wpmf_row_full">
                    <label class="ju-setting-label php_version">
                        <?php esc_html_e('PHP ', 'wpmf'); ?>
                        <?php echo esc_html(PHP_VERSION) ?>
                        <?php esc_html_e('version', 'wpmf'); ?>
                    </label>

                    <div class="right-checkbox">
                        <?php
                        if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
                            //phpcs:ignore WordPress.XSS.EscapeOutput -- Echo icon html
                            echo '<i class="material-icons system-checkbox material-icons-success">check_circle</i>';
                        } elseif (version_compare(PHP_VERSION, '7.2.0', '<') &&
                                  version_compare(PHP_VERSION, '7.0.0', '>=')) {
                            echo '<img src="'.esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-notification.png').'" class="img_notification">';
                        } else {
                            echo '<i class="material-icons system-checkbox material-icons-info">info</i>';
                        }
                        ?>
                    </div>

                </div>
            </div>

            <?php if (version_compare(PHP_VERSION, '7.2.0', '<')) : ?>
                <p class="description text_left p_warning">
                    <?php esc_html_e('Your PHP version is ', 'wpmf'); ?>
                    <?php echo esc_html(PHP_VERSION) ?>
                    <?php esc_html_e('. For performance and security reasons it better to run PHP 7.2+. Comparing to previous versions the execution time of PHP 7.X is more than twice as fast and has 30 percent lower memory consumption', 'wpmf'); ?>
                </p>
            <?php else : ?>
                <p class="description text_center">
                    <?php esc_html_e('Great ! Your PHP version is ', 'wpmf'); ?>
                    <?php echo esc_html(PHP_VERSION) ?>
                </p>
            <?php endif; ?>


            <div class="wpmf_width_100 p-tb-20 wpmf_left text label_text"><?php esc_html_e('PHP Extensions', 'wpmf'); ?></div>
            <div class="ju-settings-option wpmf_width_100">
                <div class="wpmf_row_full">
                    <label class="ju-setting-label"><?php esc_html_e('Curl', 'wpmf'); ?></label>
                    <div class="right-checkbox">
                        <?php if (!in_array('curl', get_loaded_extensions())) : ?>
                            <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information.png') ?>"
                                 srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information@3x.png') ?> 3x"
                                 class="img_warning">
                        <?php else : ?>
                            <input type="checkbox" id="php_curl" name="php_curl" checked
                                   value="php_curl" disabled class="filled-in media_checkbox"/>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if (!in_array('curl', get_loaded_extensions())) : ?>
            <p class="description p_warning">
                <?php esc_html_e('PHP Curl extension has not been detected. You need to activate in order to load video in media library and for all the cloud connections (like Google Drive, Dropbox...)', 'wpmf'); ?>
            </p>
            <?php endif; ?>
            <div class="ju-settings-option wpmf_width_100">
                <div class="wpmf_row_full">
                    <label class="ju-setting-label"><?php esc_html_e('GD Library', 'wpmf'); ?></label>
                    <div class="right-checkbox">
                        <?php if (function_exists('gd_info')) : ?>
                            <input type="checkbox" id="gd_info" name="gd_info" checked
                                   value="gd_info" disabled class="filled-in media_checkbox"/>
                        <?php else : ?>
                            <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information.png') ?>"
                                 srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/icon-information/icon-information@3x.png') ?> 3x"
                                 class="img_warning">
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <?php if (!function_exists('gd_info')) : ?>
                <p class="description p_warning">
                    <?php esc_html_e('GD library is not detected. GD is an open source library related to image creation. The Watermark feature won’t work.', 'wpmf'); ?>
                </p>
            <?php endif; ?>
</div>