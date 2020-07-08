<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div id="gallery_features" class="tab-content">
    <div class="content-box">
        <div class="content-wpmf-gallery">
            <div class="ju-settings-option">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_usegellery" value="0">
                    <label data-alt="<?php esc_html_e('Enhance the WordPress default gallery system
             by adding themes and additional parameters in the gallery manager', 'wpmf'); ?>"
                           class="ju-setting-label text">
                        <?php esc_html_e('Enable the gallery feature', 'wpmf') ?>
                    </label>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" name="wpmf_usegellery" value="1"
                                <?php
                                if (isset($usegellery) && (int) $usegellery === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ju-settings-option wpmf_right m-r-0">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_option_lightboximage" value="0">
                    <label data-alt="<?php esc_html_e('Add a lightbox option on each image of your WordPress content', 'wpmf'); ?>"
                           class="ju-setting-label text"><?php esc_html_e('Enable the single image lightbox feature', 'wpmf') ?></label>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" name="wpmf_option_lightboximage"
                                   id="cb_option_lightboximage" value="1"
                                <?php
                                if (isset($option_lightboximage) && (int) $option_lightboximage === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="ju-settings-option">
                <div class="wpmf_row_full">
                    <input type="hidden" name="wpmf_usegellery_lightbox" value="0">
                    <label data-alt="<?php esc_html_e('Add lightbox to images in WordPress default  galleries', 'wpmf'); ?>"
                           class="ju-setting-label text">
                        <?php esc_html_e('Lightbox in galleries', 'wpmf') ?></label>
                    <div class="ju-switch-button">
                        <label class="switch">
                            <input type="checkbox" name="wpmf_usegellery_lightbox" value="1"
                                <?php
                                if (isset($use_glr_lightbox) && (int) $use_glr_lightbox === 1) {
                                    echo 'checked';
                                }
                                ?>
                            >
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <?php if (defined('NGG_PLUGIN_VERSION')) : ?>
                <?php
                /**
                 * Filter check capability of current user to show import nextgen gallery button
                 *
                 * @param boolean The current user has the given capability
                 * @param string  Action name
                 *
                 * @return boolean
                 *
                 * @ignore Hook already documented
                 */
                $wpmf_capability = apply_filters('wpmf_user_can', current_user_can('manage_options'), 'show_import_nextgen_gallery_button');
                if ($wpmf_capability) : ?>
                    <div class="ju-settings-option wpmf_right">
                        <div class="wpmf_row_full">
                            <label class="ju-setting-label wpmf_width_100">
                                <button type="button" id="btn_import_gallery"
                                        class="ju-button no-background orange-button waves-effect waves-light btn_import_gallery" style="float: left">
                                    <?php esc_html_e('Sync/Import NextGEN galleries', 'wpmf'); ?>
                                </button>
                                <span class="spinner" style="margin: 10px 15px; float: left;display:none"></span>
                            </label>
                        </div>
                        <p class="description p-lr-20">
                            <?php esc_html_e('Import nextGEN albums as image in folders in the media manager.
             You can then create new galleries from WordPress media manager', 'wpmf'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="wpmf_row_full">
            <div class="wrap_left">
                <div id="gallery_image_size" class="div_list media_filter_block wpmf_width_100">
                    <ul class="image_size">
                        <li class="div_list_child accordion-section control-section control-section-default open">
                            <h3 class="accordion-section-title wpmf-section-title"
                                data-title="sizes"
                                tabindex="0"><?php esc_html_e('List defaut filter size', 'wpmf') ?>
                                <i class="zmdi zmdi-chevron-up"></i>
                                <i class="zmdi zmdi-chevron-down"></i>
                            </h3>
                            <ul class="content_list_sizes">
                                <?php
                                $sizes = apply_filters('image_size_names_choose', array(
                                    'thumbnail' => __('Thumbnail', 'wpmf'),
                                    'medium'    => __('Medium', 'wpmf'),
                                    'large'     => __('Large', 'wpmf'),
                                    'full'      => __('Full Size', 'wpmf'),
                                ));
                                foreach ($sizes as $key => $size) :
                                    ?>

                                    <li class="wpmf_width_100 ju-settings-option customize-control customize-control-select item_dimension"
                                        style="display: list-item;" data-value="<?php echo esc_html($a_dimension); ?>">
                                        <div class="wpmf_row_full">
                                            <div class="pure-checkbox ju-setting-label">
                                                <input title="" id="<?php echo esc_attr($key) ?>" type="checkbox"
                                                       name="size_value[]"
                                                       value="<?php echo esc_attr($key) ?>"
                                                    <?php
                                                    if (in_array($key, $size_selected)) {
                                                        echo 'checked';
                                                    }
                                                    ?>
                                                >
                                                <label for="<?php echo esc_html($key) ?>"><?php echo esc_html($size) ?></label>
                                            </div>
                                        </div>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
                <p class="description">
                    <?php esc_html_e('Select the image size you can load in galleries.
                     Custom image size available here can be generated by 3rd party plugins', 'wpmf'); ?>
                </p>
            </div>

            <div class="wrap_right">
                <!--    setting padding     -->
                <div id="gallery_image_padding" class="div_list media_filter_block wpmf_width_100">
                    <ul class="image_size">
                        <li class="div_list_child accordion-section control-section control-section-default open">
                            <h3 class="accordion-section-title wpmf-section-title padding_title"
                                data-title="padding" tabindex="0">
                                <?php esc_html_e('Gallery themes settings', 'wpmf') ?>
                                <i class="zmdi zmdi-chevron-up"></i>
                                <i class="zmdi zmdi-chevron-down"></i>
                            </h3>
                            <div class="content_list_padding">
                                <div class="wpmf_width_100 p-d-20 ju-settings-option customize-control customize-control-select"
                                     style="display: list-item;">
                                    <div class="wpmf_row_full">
                                        <span><?php esc_html_e('Masonry Theme', 'wpmf'); ?></span>
                                        <label><?php esc_html_e('Space between images (padding)', 'wpmf') ?></label>
                                        <label>
                                            <input name="padding_gallery[wpmf_padding_masonry]"
                                                   class="padding_gallery small-text"
                                                   type="number" min="0" max="30"
                                                   value="<?php echo esc_attr($padding_masonry) ?>">
                                        </label>
                                        <label><?php esc_html_e('px', 'wpmf') ?></label>
                                    </div>

                                    <div class="wpmf_row_full">
                                        <span><?php esc_html_e('Portfolio Theme', 'wpmf'); ?></span>
                                        <label><?php esc_html_e('Space between images (padding)', 'wpmf') ?></label>
                                        <label>
                                            <input name="padding_gallery[wpmf_padding_portfolio]"
                                                   class="padding_gallery small-text"
                                                   type="number" min="0" max="30"
                                                   value="<?php echo esc_attr($padding_portfolio) ?>">
                                        </label>
                                        <label><?php esc_html_e('px', 'wpmf') ?></label>
                                    </div>
                                </div>
                                <p class="description"><?php esc_html_e('Determine the space between images', 'wpmf'); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="default_settings" class="tab-content">
    <div class="content-box usegellery content-wpmf-gallery">
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput -- Content already escaped in the method
        echo $glrdefault_settings_html;
        ?>
    </div>
</div>