<?php
/* Prohibit direct script loading */
defined('ABSPATH') || die('No direct script access allowed!');
?>
<div class="ju-settings-option wpmf_width_100 p-lr-20">
    <div class="wpmf_row_full">
        <h3 class="settings_theme_name"><?php echo esc_html($theme_label); ?></h3>
        <div class="wpmf_glr_settings">
            <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('Number of columns
                 by default in the gallery theme', 'wpmf'); ?>">
                    <?php esc_html_e('Columns', 'wpmf'); ?>
                </label>

                <label>
                    <select class="columns"
                            name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][columns]">
                        <?php for ($i = 1; $i <= 8; $i ++) { ?>
                            <option value="<?php echo esc_html($i) ?>" <?php selected((int) $settings['columns'], (int) $i) ?> >
                                <?php echo esc_html($i) ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
            </div>

            <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('Image size to load
                 by default as thumbnail', 'wpmf'); ?>">
                    <?php esc_html_e('Gallery image size', 'wpmf'); ?>
                </label>
                <label class="size">
                    <select class="size" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][size]">
                        <?php
                        $sizes_value = json_decode(get_option('wpmf_gallery_image_size_value'));
                        $sizes       = apply_filters('image_size_names_choose', array(
                            'thumbnail' => __('Thumbnail', 'wpmf'),
                            'medium'    => __('Medium', 'wpmf'),
                            'large'     => __('Large', 'wpmf'),
                            'full'      => __('Full Size', 'wpmf'),
                        ));
                        ?>

                        <?php foreach ($sizes_value as $key) : ?>
                            <?php if (!empty($sizes[$key])) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['size'], $key); ?>>
                                    <?php echo esc_html($sizes[$key]); ?>
                                </option>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </select>
                </label>
            </div>

            <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('Image size to load by default as full
                 size (opened in the lightbox)', 'wpmf'); ?>">
                    <?php esc_html_e('Lightbox size', 'wpmf'); ?>
                </label>

                <label>
                    <select class="targetsize"
                            name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][targetsize]">
                        <?php
                        $sizes = array(
                            'thumbnail' => __('Thumbnail', 'wpmf'),
                            'medium'    => __('Medium', 'wpmf'),
                            'large'     => __('Large', 'wpmf'),
                            'full'      => __('Full Size', 'wpmf'),
                        );
                        ?>

                        <?php foreach ($sizes as $key => $name) : ?>
                            <option value="<?php echo esc_attr($key); ?>"
                                <?php selected($settings['targetsize'], $key); ?>>
                                <?php echo esc_html($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('Action when the user
                 click on the image thumbnail', 'wpmf'); ?>">
                    <?php esc_html_e('Action on click', 'wpmf'); ?>
                </label>

                <label>
                    <select class="link-to" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][link]">
                        <option value="file" <?php selected($settings['link'], 'file'); ?>>
                            <?php esc_html_e('Lightbox', 'wpmf'); ?>
                        </option>
                        <option value="post" <?php selected($settings['link'], 'post'); ?>>
                            <?php esc_html_e('Attachment Page', 'wpmf'); ?>
                        </option>
                        <option value="none" <?php selected($settings['link'], 'none'); ?>>
                            <?php esc_html_e('None', 'wpmf'); ?>
                        </option>
                    </select>
                </label>
            </div>

            <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('Image gallery
                 default ordering', 'wpmf'); ?>">
                    <?php esc_html_e('Order by', 'wpmf'); ?>
                </label>

                <label>
                    <select class="wpmf_orderby"
                            name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][orderby]">
                        <option value="post__in" <?php selected($settings['orderby'], 'post__in'); ?>>
                            <?php esc_html_e('Custom', 'wpmf'); ?>
                        </option>
                        <option value="rand" <?php selected($settings['orderby'], 'rand'); ?>>
                            <?php esc_html_e('Random', 'wpmf'); ?>
                        </option>
                        <option value="title" <?php selected($settings['orderby'], 'title'); ?>>
                            <?php esc_html_e('Title', 'wpmf'); ?>
                        </option>
                        <option value="date" <?php selected($settings['orderby'], 'date'); ?>>
                            <?php esc_html_e('Date', 'wpmf'); ?>
                        </option>
                    </select>
                </label>
            </div>

            <?php if ($theme_name === 'slider_theme') : ?>
                <div class="ju-settings-option wpmf-no-shadow wpmf_width_20 p-d-10">
                    <label class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <?php esc_html_e('Transition type', 'wpmf'); ?>
                    </label>

                    <label>
                        <select class="wpmf_animation"
                                name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][animation]">
                            <option value="slide" <?php selected($settings['animation'], 'slide'); ?>>
                                <?php esc_html_e('Slide', 'wpmf'); ?>
                            </option>
                            <option value="fade" <?php selected($settings['animation'], 'fade'); ?>>
                                <?php esc_html_e('Fade', 'wpmf'); ?>
                            </option>
                        </select>
                    </label>
                </div>
                <div class="ju-settings-option wpmf-no-shadow wpmf_width_30 p-d-10">
                    <label class="wpmf_width_100 p-b-20 wpmf_left text label_text">
                        <span class="text"><?php esc_html_e('Transition duration', 'wpmf'); ?></span>
                    </label>

                    <label>
                        <input type="number"
                               name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][duration]"
                               value="<?php echo esc_attr($settings['duration']) ?>"> ms
                    </label>
                </div>

                <div class="ju-settings-option wpmf-no-shadow p-d-10">
                    <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('By default, use ascending
                 or descending order', 'wpmf'); ?>">
                        <?php esc_html_e('Order', 'wpmf'); ?>
                    </label>

                    <div class="wpmfcard">
                        <label class="radio">
                            <input id="radio1" type="radio" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][order]"
                                   value="ASC" <?php checked($settings['order'], 'ASC') ?>>
                            <span class="outer"><span class="inner"></span></span><?php esc_html_e('Ascending', 'wpmf'); ?></label>
                        <label class="radio">
                            <input id="radio2" type="radio" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][order]"
                                   value="DESC" <?php checked($settings['order'], 'DESC') ?>>
                            <span class="outer"><span class="inner"></span></span><?php esc_html_e('Descending', 'wpmf'); ?></label>
                    </div>
                </div>
                
                <div class="ju-settings-option cboption wpmf-no-shadow wpmf_width_100 p-d-10">
                    <h4><?php esc_html_e('Slider Animation', 'wpmf'); ?></h4>
                    <div>
                        <div data-value="fade" class="gallery-slider-animation ju-settings-option wpmf_width_20 wpmf-no-shadow <?php echo ($slider_animation === 'fade') ? 'animation_selected' : '' ?>">
                            <div class="wpmf_row_full">
                                <i class="material-icons wpmf_middle">
                                    blur_linear
                                </i>
                                <label class="wpmf_middle"><?php esc_html_e('Fade', 'wpmf') ?></label>
                            </div>
                        </div>

                        <div data-value="slide" class="gallery-slider-animation ju-settings-option wpmf_width_20 wpmf-no-shadow <?php echo ($slider_animation === 'slide') ? 'animation_selected' : '' ?>">
                            <div class="wpmf_row_full">
                                <?php if ($slider_animation === 'slide') { ?>
                                    <img class="wpmf_middle img_slide" src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/slide_white.png') ?>">
                                <?php } else { ?>
                                    <img class="wpmf_middle" src="<?php echo esc_url(WPMF_PLUGIN_URL . '/assets/images/slide.png') ?>">
                                <?php } ?>
                                <label class="wpmf_middle"><?php esc_html_e('Slide', 'wpmf') ?></label>
                            </div>
                        </div>
                        <div class="wpmfcard">
                            <label class="radio">
                                <input type="hidden" name="wpmf_slider_animation" class="wpmf_slider_animation"
                                       value="<?php echo esc_html($slider_animation) ?>">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="ju-settings-option wpmf-no-shadow wpmf_width_100 p-d-10">
                    <b class="ju-setting-label setting wpmf-no-padding text wpmf_left">
                        <?php esc_html_e('Automatic animation', 'wpmf'); ?>
                    </b>

                    <label class="wpmf_left">
                        <input type="hidden"
                               name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][auto_animation]"
                               value="0">
                        <span class="ju-switch-button">
                        <label class="switch">
                            <?php if (isset($settings['auto_animation']) && (int) $settings['auto_animation'] === 1) : ?>
                                <input type="checkbox"
                                       name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][auto_animation]"
                                       value="1" checked>
                            <?php else : ?>
                                <input type="checkbox"
                                       name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][auto_animation]"
                                       value="1">
                            <?php endif; ?>

                            <span class="slider round"></span>
                        </label>
                    </span>
                    </label>
                </div>
            <?php endif; ?>

            <?php if ($theme_name !== 'slider_theme') : ?>
                <div class="ju-settings-option wpmf-no-shadow p-d-10">
                    <label class="wpmf_width_100 p-b-20 wpmf_left text label_text" data-alt="<?php esc_html_e('By default, use ascending
                 or descending order', 'wpmf'); ?>">
                        <?php esc_html_e('Order', 'wpmf'); ?>
                    </label>

                    <div class="wpmfcard">
                        <label class="radio">
                            <input id="radio1" type="radio" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][order]"
                                   value="ASC" <?php checked($settings['order'], 'ASC') ?>>
                            <span class="outer"><span class="inner"></span></span><?php esc_html_e('Ascending', 'wpmf'); ?></label>
                        <label class="radio">
                            <input id="radio2" type="radio" name="wpmf_glr_settings[theme][<?php echo esc_html($theme_name) ?>][order]"
                                   value="DESC" <?php checked($settings['order'], 'DESC') ?>>
                            <span class="outer"><span class="inner"></span></span><?php esc_html_e('Descending', 'wpmf'); ?></label>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>