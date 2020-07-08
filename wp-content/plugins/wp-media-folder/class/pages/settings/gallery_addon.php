<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div id="galleryadd_default_settings" class="tab-content">
    <div class="content-box content-wpmf-general">
        <?php
        if (is_plugin_active('wp-media-folder-gallery-addon/wp-media-folder-gallery-addon.php')) {
            // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
            echo $gallery_settings_html;
        }
        ?>
    </div>
</div>

<div id="gallery_shortcode_generator" class="tab-content">
    <div class="content-box content-wpmf-general">
        <div class="wpmf_width_100 p-d-20 ju-settings-option">
            <?php
            if (is_plugin_active('wp-media-folder-gallery-addon/wp-media-folder-gallery-addon.php')) {
                // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
                echo $gallery_shortcode_html;
            }
            ?>
        </div>
    </div>
</div>

<div id="gallery_social_sharing" class="tab-content">
    <div class="content-box content-wpmf-general">
        <div class="ju-settings-option">
            <div class="wpmf_row_full">
                <input type="hidden" name="social_sharing" value="0">
                <label data-alt="<?php esc_html_e('Possibility to load social sharing buttons on hover to image in gallery', 'wpmf'); ?>"
                       class="ju-setting-label text"><?php esc_html_e('Enable social sharing buttons', 'wpmf') ?></label>
                <div class="ju-switch-button">
                    <label class="switch">
                        <input type="checkbox" name="social_sharing" value="1"
                            <?php
                            if (isset($social_sharing) && (int) $social_sharing === 1) {
                                echo 'checked';
                            }
                            ?>
                        >
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="ju-settings-option wpmf_width_100 p-lr-20">
            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 class="text font-size-19 line-height-30">
                        <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/facebook/facebook.png') ?>"
                             srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/facebook/facebook@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/facebook/facebook@3x.png') ?> 3x"
                             class="img_social">
                        <label class="social-label wpmf_middle"><?php esc_html_e('Facebook', 'wpmf') ?></label>
                    </h4>
                    <input title="facebook" type="text" name="social_sharing_link[facebook]"
                           class="regular-text" value="<?php echo esc_attr($facebook); ?>">
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 class="text font-size-19 line-height-30">
                        <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/twitter/twitter.png') ?>"
                             srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/twitter/twitter@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/twitter/twitter@3x.png') ?> 3x"
                             class="img_social">
                        <label class="social-label wpmf_middle"><?php esc_html_e('Twitter', 'wpmf') ?></label>
                    </h4>
                    <input title="twitter" type="text" name="social_sharing_link[twitter]"
                           class="regular-text" value="<?php echo esc_attr($twitter); ?>">
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 class="text font-size-19 line-height-30">
                        <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/google/google.png') ?>"
                             srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/google/google@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/google/google@3x.png') ?> 3x"
                             class="img_social">
                        <label class="social-label wpmf_middle"><?php esc_html_e('Google+', 'wpmf') ?></label>
                    </h4>
                    <input title="google" type="text" name="social_sharing_link[google]"
                           class="regular-text" value="<?php echo esc_attr($google); ?>">
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 class="text font-size-19 line-height-30">
                        <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/instagram/instagram.png') ?>"
                             srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/instagram/instagram@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/instagram/instagram@3x.png') ?> 3x"
                             class="img_social">
                        <label class="social-label wpmf_middle"><?php esc_html_e('Instagram', 'wpmf') ?></label>
                    </h4>
                    <input title="instagram" type="text" name="social_sharing_link[instagram]"
                           class="regular-text" value="<?php echo esc_attr($instagram); ?>">
                </div>
            </div>

            <div class="ju-settings-option wpmf-no-shadow">
                <div class="wpmf_row_full">
                    <h4 class="text font-size-19 line-height-30">
                        <img src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/pinterest/pinterest.png') ?>"
                             srcset="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/pinterest/pinterest@2x.png') ?> 2x, <?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/social/pinterest/pinterest@3x.png') ?> 3x"
                             class="img_social">
                        <label class="social-label wpmf_middle"><?php esc_html_e('Pinterest', 'wpmf') ?></label>
                    </h4>
                    <input title="pinterest" type="text" name="social_sharing_link[pinterest]"
                           class="regular-text" value="<?php echo esc_attr($pinterest); ?>">
                </div>
            </div>
        </div>
    </div>
</div>
